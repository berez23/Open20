<?php
/**
 */

namespace yii\jui;

use yii\helpers\Html;

/**
 * Resizable renders an resizable jQuery UI widget.
 *
 * For example:
 *
 * ```php
 * Resizable::begin([
 *     'clientOptions' => [
 *         'grid' => [20, 10],
 *     ],
 * ]);
 *
 * echo 'Resizable contents here...';
 *
 * Resizable::end();
 * ```
 *
 * @since 2.0
 */
class Resizable extends Widget
{
    /**
     * @inheritdoc
     */
    protected $clientEventMap = [
        'create' => 'resizecreate',
        'resize' => 'resize',
        'start' => 'resizestart',
        'stop' => 'resizestop',
    ];


    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        echo Html::beginTag('div', $this->options) . "\n";
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::endTag('div') . "\n";
        $this->registerWidget('resizable');
    }
}
