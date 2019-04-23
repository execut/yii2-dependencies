<?php
/**
 */

namespace execut\dependencies;


use yii\base\Exception;

class PluginBehavior extends \yii\base\Behavior
{
    protected $_plugins = [];
    public $pluginInterface = null;
    public function setPlugins($plugins) {
        $this->_plugins = $plugins;
    }

    protected $_pluginsIsInited = false;
    protected function initPlugins() {
        if (!is_string($this->pluginInterface)) {
            throw new Exception('You may set pluginInterface via behavior config');
        }

        if (!$this->_pluginsIsInited) {
            foreach ($this->_plugins as $key => $plugin) {
                if (is_array($plugin)) {
                    $plugin = \yii::createObject($plugin);
                    $this->_plugins[$key] = $plugin;
                }

                if (!($plugin instanceof $this->pluginInterface)) {
                    throw new Exception('Plugin for module ' . $this->owner->id . ' ' . get_class($plugin) . ' must bee instanceof ' . $this->pluginInterface);
                }

                if (method_exists($plugin, 'attach')) {
                    $plugin->attach($this->owner);
                }
            }

            $this->_pluginsIsInited = true;
        }
    }

    public function getPlugins() {
        $this->initPlugins();
        return $this->_plugins;
    }

    public function getPlugin($name) {
        return $this->getPlugins()[$name];
    }

    public function getPluginsResults($function, $isFirstResult = false, $arguments = []) {
        $result = null;
        foreach ($this->getPlugins() as $plugin) {
            $pluginResult = call_user_func_array([$plugin, $function], $arguments);
            if ($isFirstResult && $pluginResult) {
                return $pluginResult;
            }

            if (!$isFirstResult && is_array($pluginResult)) {
                if ($result === null) {
                    $result = [];
                }

                $result = array_merge($result, $pluginResult);
            }
        }

        return $result;
    }
}