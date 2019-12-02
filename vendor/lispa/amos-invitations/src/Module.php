<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\invitations
 * @category   CategoryName
 */

namespace lispa\amos\invitations;

use lispa\amos\core\module\AmosModule;
use lispa\amos\core\module\ModuleInterface;
use lispa\amos\invitations\widgets\icons\WidgetIconInvitations;
use lispa\amos\invitations\widgets\icons\WidgetIconInvitationsAll;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package lispa\amos\invitations
 */
class Module extends AmosModule implements ModuleInterface
{
    public static $CONFIG_FOLDER = 'config';

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';

    /**
     * @var string $name
     */
    public $name = 'invitations';

    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return "invitations";
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::setAlias('@lispa/amos/' . static::getModuleName() . '/controllers', __DIR__ . '/controllers');

        //Configuration: merge default module configurations loaded from config.php with module configurations set by the application
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . self::$CONFIG_FOLDER . DIRECTORY_SEPARATOR . 'config.php');
        \Yii::configure($this, ArrayHelper::merge($config, $this));
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconInvitations::className(),
            WidgetIconInvitationsAll::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return [];
    }

    /**
     * Get default model classes
     */
    protected function getDefaultModels()
    {
        return [
            'Invitation' => __NAMESPACE__ . '\\' . 'models\Invitation',
            'InvitationUser' => __NAMESPACE__ . '\\' . 'models\InvitationUser',
        ];
    }
}
