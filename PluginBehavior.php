<?php
/**
 */

namespace execut\dependencies;


use execut\yii\base\Exception;

class PluginBehavior extends \yii\base\Behavior
{
    protected $_plugins = [];
    public $pluginInterface = null;
    public function setPlugins($plugins) {
        $this->_plugins = $plugins;
    }

    protected $_pluginsIsInited = false;
    protected function initPlugins() {
        if (!$this->_pluginsIsInited) {
            foreach ($this->_plugins as $key => $plugin) {
                if (is_array($plugin)) {
                    $plugin = \yii::createObject($plugin);
                    $this->_plugins[$key] = $plugin;
                }

                if (!is_subclass_of($plugin, $this->pluginInterface)) {
                    throw new Exception('Plugin for module ' . $this->owner->id . ' ' . get_class($plugin) . ' must bee instanceof ' . $this->pluginInterface);
                }
            }

            $this->_pluginsIsInited = true;
        }
    }

    public function getPlugins() {
        $this->initPlugins();
        return $this->_plugins;
    }

    public function getPluginsResults($function, $isFirstResult = false) {
        $result = [];
        foreach ($this->getPlugins() as $plugin) {
            $pluginResult = $plugin->$function();
            if ($isFirstResult) {
                return $result;
            }

            $result = array_merge($result, $pluginResult);
        }

        return $result;
    }
}