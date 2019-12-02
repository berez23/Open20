<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core
 * @category   CategoryName
 */

namespace lispa\amos\layout\assets;

use Yii;
use yii\web\AssetBundle;

class StickySidebarAsset extends AssetBundle
{
    /**
     * [$sourcePath description]
     * @var string
     */
    public $sourcePath = '@bower/sticky-sidebar/dist';

    /**
     * [$css description]
     * @var array
     */
    public $css = [
    ];

    /**
     * [$js description]
     * @var array
     */
    public $js = [
        'sticky-sidebar.js',
    ];

    /**
     * [$depends description]
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset'
    ];
}
