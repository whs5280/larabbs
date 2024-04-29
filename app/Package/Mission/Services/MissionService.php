<?php

namespace App\Package\Mission\Services;

use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Handlers\AbstractHandler;
use App\Package\Mission\Models\Mission;
use App\Package\Mission\Repositories\MissionGroupRepository;
use Illuminate\Support\Collection;

/**
 * 任务服务
 *
 * @package App\Package\Mission\Services
 */
class MissionService
{
    /**
     * @var MissionGroupRepository
     */
    protected $groupRepo;

    /**
     * @param MissionGroupRepository $groupRepo
     */
    public function __construct(MissionGroupRepository $groupRepo)
    {
        $this->groupRepo = $groupRepo;
    }

    /**
     * 任务列表
     *
     * @param int $groupId
     * @param string $tag
     * @param MissionAcceptable $acceptable
     * @return Collection
     */
    public function index(int $groupId, string $tag, MissionAcceptable $acceptable): Collection
    {
        $group = $this->groupRepo->firstByGroupIdAndTag($groupId, $tag);
        if (is_null($group)) {
            return collect();
        }
        $missions = $group->findActiveMissions();

        $handlers = collect();
        foreach ($missions as $mission) {
            /** @var Mission $mission */
            $handler = $mission->makeHandler($acceptable);
            /** @var AbstractHandler $handler */
            $handler->dispatchToHandle();
            $handlers->push($handler);
        }

        return $handlers->map(function (AbstractHandler $handler) {
            return $this->transformHandler($handler);
        });
    }

    /**
     * 接受任务
     *
     * @param Mission $mission
     * @param MissionAcceptable $acceptable
     * @return mixed
     */
    public function receive(Mission $mission, MissionAcceptable $acceptable)
    {
        return $mission->makeHandler($acceptable)->receive();
    }

    /**
     * 领取奖励
     *
     * @param Mission $mission
     * @param MissionAcceptable $acceptable
     * @return mixed
     */
    public function reward(Mission $mission, MissionAcceptable $acceptable)
    {
        return $mission->makeHandler($acceptable)->reward();
    }

    /**
     * 数据格式化
     *
     * @param AbstractHandler $handler
     * @return array
     */
    protected function transformHandler(AbstractHandler $handler): array
    {
        $mission = $handler->getMission();
        return [
            'id'   => $mission->getKey(),
            'type' => $mission->getMissionType(),
            'name' => $mission->getMissionName(),
            'link' => $handler->isJumpLink() ? $handler->getJumpLink() : '',
            'description'  => $mission->getMissionDescription(),
            'reward_type'  => $mission->getMissionRewardType(),
            'reward_count' => $mission->getMissionRewardCount(),
            'btn_status'   => $handler->isReward() ? 'REWARD' : ($handler->isFinish() ? 'FINISH' : 'RECEIVE'),
            'has_btn'      => $handler->hasBtn(),
            'is_jump_link' => $handler->isJumpLink(),
            'is_share'     => $handler->isShare(),
        ];
    }
}
