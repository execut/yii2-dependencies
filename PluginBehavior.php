<?php
/**
 */

namespace execut\dependencies;


use yii\base\Exception;
use yii\helpers\ArrayHelper;

class PluginBehavior extends \yii\base\Behavior
{
    protected $_plugins = [];
    public $pluginInterface = null;
    public function setPlugins($plugins) {
        $this->_plugins = $plugins;
    }

    protected $_pluginsIsInited = false;
    protected function initPlugins() {
        $pluginInterfaces = $this->pluginInterface;
        if (!(is_array($pluginInterfaces) || is_string($pluginInterfaces)) || empty($pluginInterfaces)) {
            throw new Exception('You may set pluginInterface via behavior config');
        }

        if (is_string($pluginInterfaces)) {
            $pluginInterfaces = [$pluginInterfaces];
        }

        if (!$this->_pluginsIsInited) {
            foreach ($this->_plugins as $key => $plugin) {
                if (is_array($plugin)) {
                    $plugin = \yii::createObject($plugin);
                    $this->_plugins[$key] = $plugin;
                }

                $isHasGood = false;
                foreach ($pluginInterfaces as $pluginInterface) {
                    if ($plugin instanceof $pluginInterface) {
                        $isHasGood = true;
                        break;
                    }
                }

                if (!$isHasGood) {
                    throw new Exception('Plugin for module ' . $this->owner->id . ' ' . get_class($plugin) . ' must bee instanceof ' . implode(' or ', $pluginInterfaces));
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
            if (!method_exists($plugin, $function)) {
                continue;
            }

            $pluginResult = call_user_func_array([$plugin, $function], $arguments);
            if ($isFirstResult && $pluginResult) {
                return $pluginResult;
            }

            if (!$isFirstResult && is_array($pluginResult)) {
                if ($result === null) {
                    $result = [];
                }

                $result = ArrayHelper::merge($result, $pluginResult);
            }
        }

        return $result;
    }
}