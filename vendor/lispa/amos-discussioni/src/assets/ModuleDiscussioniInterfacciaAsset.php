<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

namespace lispa\amos\discussioni\assets;

use yii\web\AssetBundle;
use lispa\amos\core\widget\WidgetAbstract;

class ModuleDiscussioniInterfacciaAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lispa/amos-discussioni/src/assets/web/';

    public $css = [
        'less/discussions.less',
    ];
    public $js = [
        'js/discussioni-interfaccia-js.js'
    ];
    public $depends = [
    ];

    public function init()
    {
        $moduleL = \Yii::$app->getModule('layout');

        if(!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS){
            $this->css = ['less/discussions_fullsize.less'];
        }

        if(!empty($moduleL))
        { $this->depends [] = 'lispa\amos\layout\assets\BaseAsset'; }
        else
        { $this->depends [] = 'lispa\amos\core\views\assets\AmosCoreAsset'; }
        parent::init(); // TODO: Change the autogenerated stub
    }
}
