<?php

namespace App\Package\Mission\Handlers;

use App\Package\Mission\Exceptions\MissionHandlerNotFoundException;
use Closure;
use Illuminate\Contracts\Foundation\Application;

/**
 * 任务处理器管理器
 */
class MissionHandlerManager
{
    protected $app;

    protected $customHandlers = [];

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function extend(string $name, Closure $creator)
    {
        $this->customHandlers[$name] = $creator;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function handler(string $name)
    {
        if (isset($this->customHandlers[$name])) {
            return $this->customHandlers[$name]($this->app, $name);
        } else {
            $method = 'create' . ucfirst($name) . 'Handler';
            if (method_exists($this, $method)) {
                return $this->{$method}();
            }
            throw new MissionHandlerNotFoundException("handler {$name} is not supported.");
        }
    }

    protected function createOnceClickHandler(): OnceClickHandler
    {
        return new OnceClickHandler();
    }
}
