<?php

namespace App\Package\Mission\Periods;

use App\Package\Mission\Exceptions\MissionPeriodNotFoundException;
use Closure;
use Illuminate\Contracts\Foundation\Application;

/**
 * 任务周期管理器
 *
 * Class MissionPeriodManager
 * @package App\Package\Mission\Periods
 */
class MissionPeriodManager
{
    protected $app;

    protected $customPeriods = [];

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function extend(string $name, Closure $creator)
    {
        $this->customPeriods[$name] = $creator;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function period(string $name)
    {
        if (isset($this->customPeriods[$name])) {
            return $this->customPeriods[$name]($this->app, $name);
        } else {
            $method = 'create' . ucfirst($name) . 'Period';
            if (method_exists($this, $method)) {
                return $this->{$method}();
            }
            throw new MissionPeriodNotFoundException("period {$name} is not supported.");
        }
    }

    public function createOncePeriod(): Once
    {
        return new Once();
    }

    public function createDailyPeriod(): Daily
    {
        return new Daily();
    }

    public function createWeeklyPeriod(): Weekly
    {
        return new Weekly();
    }

    public function createMonthlyPeriod(): Monthly
    {
        return new Monthly();
    }

    public function createYearlyPeriod(): Yearly
    {
        return new Yearly();
    }
}
