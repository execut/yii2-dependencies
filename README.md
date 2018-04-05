# yii2-dependencies
Инструменты для изолирования связей модулей.
Например, есть модуль пользователей и внутри него есть связь с другим модулем в виде получения:


В модуль необходимо добавить поддержку плагинов с помощью харакстеристики PluginBehavior и вынести этот функционал
в плагин через реализацию интерфейса модуля Plugin:
```php
<?php
namespace execut\users;

interface Plugin
{
    public function sendRecoveryMessage($user);
}
```

```php
<?php
namespace execut\users;

use execut\dependencies\PluginBehavior;
use execut\users\Plugin;
/**
 * Class Module
 *
 * @mixin PluginBehavior
 * @package execut\userTags
 */
class Module extends \yii\base\Module implements Plugin
{
    public function behaviors()
    {
        return [
            'plugin' => [
                'class' => PluginBehavior::class,
                'pluginInterface' => Plugin::class,
            ],
        ];
    }

    public function sendRecoveryMessage($user) {
        return $this->getPluginsResults(__FUNCTION__, false, func_get_args());
    }
}
```