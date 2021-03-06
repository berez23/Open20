<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\layout
 * @category   CategoryName
 */

namespace lispa\amos\layout\assets;

use lispa\amos\core\widget\WidgetAbstract;
use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package lispa\amos\layout\assets
 */
class BaseAsset extends AssetBundle
{
    public $js = [
        'js/bootstrap-tabdrop.js',
        'js/globals.js',
        'js/device-detect.js',
        'js/tooltip-component.js',
        'js/footer.js'
    ];

    public $css = [
        'less/main.less'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'lispa\amos\layout\assets\LajaxAsset',
        'yii\bootstrap\BootstrapAsset',
        'kartik\select2\Select2Asset',
        'lispa\amos\layout\assets\IEAssets',
        'lispa\amos\layout\assets\JqueryUiTouchPunchImprovedAsset',
        'lispa\amos\layout\assets\ConflictJuiBootstrap',
        'lispa\amos\layout\assets\TourAsset',
        'lispa\amos\layout\assets\IconAsset',
        'lispa\amos\layout\assets\FontAsset',
        'lispa\amos\layout\assets\DialogAsset',
        'lispa\amos\layout\assets\LajaxAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/resources/base';
        if(!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS){
            $this->css = ['less/main_fullsize.less'];
        }
        parent::init();
    }
}
