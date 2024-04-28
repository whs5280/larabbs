<?php

namespace App\Package\Mission\Handlers;

use App\Package\Mission\Contracts\Mission;
use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Contracts\MissionHandler;
use App\Package\Mission\Exceptions\MissionReceivedException;
use App\Package\Mission\Exceptions\MissionRewardException;
use App\Package\Mission\Facades\MissionPeriod;
use App\Package\Mission\Facades\MissionRepo;
use App\Package\Mission\Jobs\HandleMission;
use App\Package\Mission\Models\MissionRecord;
use Illuminate\Cache\RedisLock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 任务处理器抽象类
 */
abstract class AbstractHandler implements MissionHandler
{
    /**
     * 最大队列分发次数
     */
    const MAX_DISPATCH_COUNT = 3;

    const MISSION_RECEIVE = 0b100;
    const MISSION_FINISH = 0b010;
    const MISSION_REWARD = 0b001;

    /**
     * @var Mission
     */
    protected $mission;

    /**
     * @var MissionAcceptable
     */
    protected $acceptable;

    public function setMission(Mission $mission)
    {
        $this->mission = $mission;
        return $this;
    }

    public function setMissionAcceptable(MissionAcceptable $missionAcceptable)
    {
        $this->acceptable = $missionAcceptable;
        return $this;
    }

    public function getMission(): Mission
    {
        return $this->mission;
    }

    public function getMissionAcceptable(): MissionAcceptable
    {
        return $this->acceptable;
    }

    protected function getCacheKeyPrefix(): string
    {
        return vsprintf('group:%s:missions:%s:%s:%s', [
            $this->mission->getMissionGroupId(),
            $this->mission->getKey(),
            $this->acceptable->getMissionAcceptableType(),
            $this->acceptable->getMissionAcceptableKey()
        ]);
    }

    protected function getDispatchCacheKey(): string
    {
        return $this->getCacheKeyPrefix() . ':dispatch';
    }

    public function incrementDispatchCount()
    {
        Cache::increment($this->getDispatchCacheKey());
    }

    public function decrementDispatchCount()
    {
        $dispatchCount = Cache::decrement($this->getDispatchCacheKey());
        if ($dispatchCount <= 0) {
            Cache::forget($this->getDispatchCacheKey());
        }
        return $dispatchCount;
    }

    protected function needDispatch(): bool
    {
        $dispatchCount = Cache::get($this->getDispatchCacheKey());
        return is_null($dispatchCount) || $dispatchCount < self::MAX_DISPATCH_COUNT;
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        $this->handleWithLock();
    }

    protected function handleWithLock()
    {
        /** @var RedisLock $lock */
        $lock = Cache::store('redis')->lock($this->getCacheKeyPrefix(). ':lock', 20);
        return $lock->get(function () {
            return $this->handleMission();
        });
    }

    protected function handleMission(): bool
    {
        if (!$this->canHandle()) {
            return false;
        }
        if (!$this->check()) {
            return false;
        }
        return $this->finish();
    }

    protected function canHandle(): bool
    {
        // 任务上架并且已接受但未完成未领奖
        return $this->mission->isActive()
            && $this->isReceived(false)
            && !$this->isFinish(false)
            && !$this->isReward(false);
    }

    public function isReceived($viaCache = true)
    {
        return $this->getMissionStatus(self::MISSION_RECEIVE, $viaCache);
    }

    public function isFinish($viaCache = true)
    {
        return $this->getMissionStatus(self::MISSION_FINISH, $viaCache);
    }

    public function isReward($viaCache = true)
    {
        return $this->getMissionStatus(self::MISSION_REWARD, $viaCache);
    }

    protected function getMissionStatus($key, bool $viaCache = true)
    {
        return $this->processWithMissionStatusLock(function () use (&$key, &$viaCache) {
            $methods = [
                self::MISSION_RECEIVE => 'isReceiveMission',
                self::MISSION_FINISH  => 'isFinishMission',
                self::MISSION_REWARD  => 'isRewardMission',
            ];
            $cacheKey = $this->getMissionStatusCacheKey();
            $status = Cache::get($cacheKey);
            if ($viaCache || isset($status[$key])) {
                return $status[$key] ?? false;
            }
            $status[$key] = MissionRepo::recordRepo()->{$methods[$key]}($this->acceptable, $this->mission);
            Cache::put($cacheKey, $status, $this->getCacheTtl());
            return $status[$key];
        });
    }

    protected function processWithMissionStatusLock($callback)
    {
        /** @var RedisLock $lock */
        $lock = Cache::store('redis')->lock($this->getCacheKeyPrefix() . 's:lock', 5);
        try {
            return $lock->block(5, $callback);
        } catch (LockTimeoutException $e) {
            throw new \RuntimeException(trans('mission::msg.E405007'));
        }
    }

    protected function getMissionStatusCacheKey(): string
    {
        return $this->getCacheKeyPrefix() . ':s';
    }

    /**
     * 任务周期的缓存时间
     *
     * @return int
     */
    protected function getCacheTtl(): int
    {
        return MissionPeriod::period($this->mission->getMissionPeriodType())
            ->cacheTtl($this->acceptable, $this->mission);
    }

    public function receive(): bool
    {
        if ($this->isFinish(false)) {
            throw new MissionReceivedException(trans('mission::msg.E405004'), 405004);
        }
        if (!$this->isReceived(false) && !$this->saveReceive()) {
            throw new MissionReceivedException(trans('mission::msg.E405006'), 405006);
        }
        return $this->dispatchToHandle();
    }

    protected function saveReceive()
    {
        try {
            return DB::transaction(function () {
                $this->changeMissionStatus(self::MISSION_RECEIVE, true);
                $this->saveMissionRecord(MissionRecord::STATUS_RECEIVE);
                return true;
            });
        } catch (\Throwable $e) {
            $this->changeMissionStatus(self::MISSION_RECEIVE, false);
            logger()->error('领取任务失败：' . $e->getMessage());
            return false;
        }
    }

    protected function dispatchToHandle(): bool
    {
        if (!$this->needDispatch()) {
            return false;
        }
        HandleMission::dispatch($this)->onQueue(config('app.name') . '-handle-mission');
        $this->incrementDispatchCount();
        return true;
    }

    public function finish()
    {
        try {
            return DB::transaction(function () {
                $this->changeMissionStatus(self::MISSION_FINISH, true);
                $this->saveMissionRecord(MissionRecord::STATUS_FINISH);
                $this->changeMissionStatus(self::MISSION_RECEIVE, false);
                Cache::put($this->getHasRewardCacheKey(), true, now()->addMonth());
                return true;
            });
        } catch (\Throwable $e) {
            $this->changeMissionStatus(self::MISSION_FINISH, false);
            logger()->error('完成任务处理失败：' . $e->getMessage());
            return false;
        }
    }

    protected function changeMissionStatus($key, $value)
    {
        return $this->processWithMissionStatusLock(function () use (&$key, &$value) {
            $cacheKey = $this->getMissionStatusCacheKey();
            $status = Cache::get($cacheKey) ?? [$key => $value];
            $status[$key] = $value;
            Cache::put($cacheKey, $status, $this->getCacheTtl());
        });
    }

    protected function saveMissionRecord($status)
    {
        $attrs = [
            'acceptable_id'   => $this->acceptable->getMissionAcceptableKey(),
            'acceptable_type' => $this->acceptable->getMissionAcceptableType(),
            'group_id'     => $this->mission->getMissionGroupId(),
            'mission_id'   => $this->mission->getKey(),
            'reward_type'  => $this->mission->getMissionRewardType(),
            'reward_count' => $this->mission->getMissionRewardCount(),
            'status' => $status,
        ];

        // 保存完成记录
        $missionRecord = MissionRepo::recordRepo()->create($attrs);
        $missionRecord->setRelation('mission', $this->mission);
        $missionRecord->setRelation('acceptable', $this->acceptable);
        return $missionRecord;
    }

    protected function getHasRewardCacheKey(): string
    {
        return $this->getCacheKeyPrefix() . ':reward';
    }

    public function reward()
    {
        if ($this->isReward(false)) {
            throw new MissionRewardException(trans('mission::msg.E405001'), 405001);
        }
        if (!$this->isFinish(false)) {
            throw new MissionRewardException(trans('mission::msg.E405002'), 405002);
        }
        try {
            return DB::transaction(function () {
                $this->changeMissionStatus(self::MISSION_REWARD, true);
                $this->saveMissionRecord(MissionRecord::STATUS_REWARD);
                Cache::put($this->getHasRewardCacheKey(), false, now()->addMonth());
                return true;
            });
        } catch (\Throwable $e) {
            $this->changeMissionStatus(self::MISSION_REWARD, false);
            logger()->error('完成任务领奖处理失败：' . $e->getMessage());
            throw new MissionRewardException(trans('mission::msg.E405003'), 405003);
        }
    }

    protected abstract function check();

    protected abstract function isShare();
}
