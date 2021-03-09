<?php


namespace execut\dependencies;


use Codeception\Test\Unit;

class PluginBehaviorTest extends Unit implements PluginBehaviorTestOkInterface
{
    public $id = 'test';
    public function testBadInstanceException() {
        $behavior = new PluginBehavior([
            'owner' => $this,
            'plugins' => [
                [
                    'class' => self::class,
                ],
            ],
            'pluginInterface' => PluginBehaviorTestInterface::class,
        ]);
        $this->expectExceptionMessage('Plugin for module test ' . self::class . ' must bee instanceof ' . PluginBehaviorTestInterface::class);
        $behavior->getPlugins();
    }

    public function testBadInstanceExceptionFromArray() {
        $behavior = new PluginBehavior([
            'owner' => $this,
            'plugins' => [
                [
                    'class' => self::class,
                ],
            ],
            'pluginInterface' => [
                PluginBehaviorTestInterface::class,
                PluginBehaviorTestOtherInterface::class,
            ],
        ]);
        $this->expectExceptionMessage('Plugin for module test ' . self::class . ' must bee instanceof ' . PluginBehaviorTestInterface::class . ' or ' . PluginBehaviorTestOtherInterface::class);
        $behavior->getPlugins();
    }

    public function testWithoutInstanceException() {
        $behavior = new PluginBehavior([
            'owner' => $this,
            'plugins' => [
                [
                    'class' => self::class,
                ],
            ],
            'pluginInterface' => [
            ],
        ]);

        $this->expectExceptionMessage('You may set pluginInterface via behavior config');
        $behavior->getPlugins();
    }

    public function testGoodInstanceFromArray() {
        $behavior = new PluginBehavior([
            'owner' => $this,
            'plugins' => [
                [
                    'class' => self::class,
                ],
            ],
            'pluginInterface' => [
                PluginBehaviorTestInterface::class,
                PluginBehaviorTestOkInterface::class
            ],
        ]);
        $this->assertIsArray($behavior->getPlugins());
    }

    public function testAddPlugins() {
        $behavior = new PluginBehavior([
            'pluginInterface' => PluginBehaviorTestOkInterface::class,
        ]);
        $behavior->addPlugins([
            'test' => [
                'class' => self::class,
            ],
        ]);
        $this->assertInstanceOf(self::class, $behavior->getPlugin('test'));
    }

    public function testAddPluginFromConfiguration()
    {
        $behavior = new PluginBehavior([
            'pluginInterface' => PluginBehaviorTestOkInterface::class,
        ]);

        $behavior->addPlugin('test', [
            'class' => self::class,
        ]);

        $this->assertInstanceOf(self::class, $behavior->getPlugin('test'));
    }

    public function testAddPluginInstance()
    {
        $behavior = new PluginBehavior([
            'pluginInterface' => PluginBehaviorTestOkInterface::class,
        ]);

        $behavior->addPlugin('test', $this);

        $this->assertInstanceOf(self::class, $behavior->getPlugin('test'));
    }
}

interface PluginBehaviorTestInterface {
}

interface PluginBehaviorTestOtherInterface {
}

interface PluginBehaviorTestOkInterface {
}