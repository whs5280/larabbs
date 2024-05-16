<?php

namespace App\Container\base;

use Exception;
use ReflectionClass;

class Container
{
    /**
     * 单例数据
     * @var array
     */
    private $_singletons = [];

    /**
     * 依赖数组
     * @var array
     */
    private $_definitions = [];

    /**
     * 容器所有类的实例对象
     * @var array
     */
    private $_reflections = [];

    /**
     * 绑定操作
     *
     * @param string $class 实例ID
     * @param array $definition 依赖数组，即配置文件
     * @return $this
     * @throws Exception
     */
    public function set(string $class, array $definition = []): Container
    {
        $this->_definitions[$class] = $this->normalizeDefinition($class, $definition);
        unset($this->_singletons[$class]);
        return $this;
    }

    /**
     * 从容器中获取实例
     *
     * @param string $class
     * @param array $config
     * @return mixed|object|void|null
     * @throws Exception
     */
    public function get(string $class, array $config)
    {
        if (isset($this->_singletons[$class])) {
            return $this->_singletons[$class];
        }

        if (isset($this->_definitions[$class])){
            $definition = $this->_definitions[$class];
            if (is_array($definition)) {
                $concrete = $definition['class'];
                unset($definition['class']);
                $config = array_merge($definition, $config);
                $object = $this->build($concrete, $config);
                // 绑定到单例数组
                if (!array_key_exists($class, $this->_singletons)) {
                    $this->_singletons[$class] = $object;
                }
                return $object;
            } elseif (is_object($definition)) {
                return $this->_singletons[$class] = $definition;
            } else {
                throw new Exception("The definition of {$class} is not a class or object");
            }
        }
    }

    /**
     * 创建实例
     *
     * @param $class
     * @param $config
     * @return object|null
     * @throws Exception
     */
    private function build($class, $config): ?object
    {
        /**
         * @var $reflection ReflectionClass
         */
        $reflection = $this->getReflection($class);
        if (!$reflection->isInstantiable()) {
            throw new Exception("This class {$reflection->name} not can instance");
        }

        try {
            if (empty($config)) {
                $object = $reflection->newInstance();
            } else {
                $object = $reflection->newInstanceArgs($config);
            }
        } catch (\ReflectionException $e) {
            throw new Exception($e->getMessage());
        }

        /*if (method_exists($object, 'init')) {
            $object->init();
        }*/
        $this->_reflections[$class] = $object;

        return $object;
    }

    /**
     * 返回某个类的实例
     *
     * @throws Exception
     */
    private function getReflection($class)
    {
        if (!isset($this->_reflections[$class])) {
            try {
                $this->_reflections[$class] = new ReflectionClass($class);
            } catch (\ReflectionException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return $this->_reflections[$class];
    }

    /**
     * 验证依赖
     *
     * @param $class
     * @param $definition
     * @return array|string[]
     * @throws Exception
     */
    private function normalizeDefinition($class, $definition): array
    {
        if (empty($definition)) {
            return ['class' => $class];
        }
        if (is_string($definition)) {
            return ['class' => $definition];
        }
        if (is_array($definition)) {
            if (!isset($definition['class'])) {
                if (strpos($class,'\\') !== false){
                    $definition['class'] = $class;
                }else{
                    throw new Exception('A class definition requires a "class" member.');
                }
            }
            return $definition;
        }
        throw new Exception("Unsupported definition type for \"$class\": " . gettype($definition));
    }

    /**
     * 解除绑定
     *
     * @param $class
     * @return void
     */
    private function clear($class)
    {
        unset($this->_singletons[$class], $this->_definitions[$class]);
    }
}
