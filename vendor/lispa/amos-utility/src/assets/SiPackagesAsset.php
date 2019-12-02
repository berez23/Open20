<?php

namespace lispa\amos\utility\assets;

use yii\web\AssetBundle;

class SiPackagesAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/lispa/amos-utility/src/assets/web';
	
    /**
     * @inheritdoc
     */
    public $css = [
    ];
	
    /**
     * @inheritdoc
     */
    public $js = [
        'js/d3.min.js',
        'js/d3.dependencyWheel.js',
        'js/composerBuilder.js',
    ];
	
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        $moduleL = \Yii::$app->getModule('layout');
        if(!empty($moduleL))
        { $this->depends [] = 'lispa\amos\layout\assets\BaseAsset'; }
        else
        { $this->depends [] = 'lispa\amos\core\views\assets\AmosCoreAsset'; }
        parent::init(); // TODO: Change the autogenerated stub
    }
}