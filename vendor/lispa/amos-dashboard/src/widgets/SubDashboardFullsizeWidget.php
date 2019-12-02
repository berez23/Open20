<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\dashboard
 * @category   CategoryName
 */

namespace lispa\amos\dashboard\widgets;

use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use Yii;
use yii\base\Widget;
use lispa\amos\dashboard\AmosDashboard;
use lispa\amos\dashboard\models\search\AmosWidgetsSearch;
use lispa\amos\dashboard\models\AmosWidgets;
use lispa\amos\dashboard\assets\SubDashboardAsset;

/**
 * Class SubDashboardFullsizeWidget
 * @package lispa\amos\dashboard\widgets
 */
class SubDashboardFullsizeWidget extends Widget
{
    public $model;
    public $module;
    public $sub_dashboard     = 1;
    public $widgets_type      = 'ICON';
    public $classDivGraphic   = '';
    public $graphicCustomSize = true;

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (empty($this->model)) {
            throw new \Exception(AmosDashboard::t('amosdashbaord', 'Missing model'));
        }
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $widgets = '';
        if (empty($this->module)) {
            $this->module = $this->getModule();
            if (!empty($this->module)) {
                if ($this->widgets_type == AmosWidgets::TYPE_ICON) {
                    $widgets = AmosWidgetsSearch::selectableIcon($this->sub_dashboard, $this->module, true);
                    if ($widgets->count()) {
                        foreach ($widgets->all() as $value) {
                            if ($value->classname != 'lispa\amos\admin\widgets\icons\WidgetIconUserProfile' &&
                                $value->classname != 'lispa\amos\community\widgets\icons\WidgetIconCommunityDashboard' &&
                                (
                                    empty(\Yii::$app->params['isPoi']) || (!empty(\Yii::$app->params['isPoi']) && \Yii::$app->params['isPoi']
                                        == false) || $this->model->className() != \lispa\amos\community\models\Community::className()
                                    || ($value->classname != \lispa\amos\projectmanagement\widgets\icons\WidgetIconprojects::className()
                                        /* && $value->classname !=  \lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard::className() */)
                                    || ($value->classname == \lispa\amos\projectmanagement\widgets\icons\WidgetIconprojects::className()
                                        && ($this->model->id != 2751 && $this->model->id != 2754 && $this->model->id != 2750))
                                    /* || ($value->classname ==  \lispa\amos\documenti\widgets\icons\WidgetIconDocumentiDashboard::className()
                                      && ($this->model->id != 2751)) */
                                )) {
                                $widget = Yii::createObject($value->classname);
                                echo $widget::widget();
                            }
                        }
                    }
                } else if ($this->widgets_type == AmosWidgets::TYPE_GRAPHIC) {
                    $widgets = AmosWidgetsSearch::selectableGraphic($this->sub_dashboard, $this->module, true);
                    $this->runHtml($widgets, AmosWidgets::TYPE_GRAPHIC);
                }
            }
        }
    }

    /**
     * This method render the widget graphics
     * @param type $widgets
     */
    protected function runHtml($widgets)
    {
        $moduleL         = \Yii::$app->getModule('layout');
        $layoutModuleSet = isset($moduleL);
        $showWidgets     = [];

        if ($widgets->count()) {
            if ($this->graphicCustomSize) {
                foreach ($widgets->all() as $value) {
                    $widgetGraphic = Yii::createObject($value->classname);
                    if (empty($widgetGraphic->classFullSize)) {
                        array_unshift($showWidgets, $widgetGraphic);
                    } else {
                        $showWidgets[] = $widgetGraphic;
                    }
                }
            }
            SubDashboardAsset::register(\Yii::$app->getView());
            \Yii::$app->getView()->registerJs("
                if($('.grid').length) {
                    $('.grid').masonry({
                        itemSelector: '.grid-item',
                        columnWidth: '.grid-sizer',
                        percentPosition: true,
                        columnWidth: '.grid-sizer',
                        gutter: 10
                    });
                }
                ", \yii\web\View::POS_READY);
            echo '<div id="bk-pluginGrafici" class="sub-dashboard-graphics wrap-graphic-widget">'.
            '<div id="widgets-graphic">';
            foreach ($showWidgets as $widget) {
                echo '<div '.(($layoutModuleSet) ? '' : 'class="'.$this->classDivGraphic.'');
                if ($this->graphicCustomSize === true) {
                    if (!empty($widget->classFullSize)) {
                        echo ' '.$widget->classFullSize;
                    }
                }
                echo '" '.
                ' data-code="'.$widget::classname().'" data-module-name="'.$widget->moduleName.'">'.$widget::widget().'</div>';
            }
            echo '</div>'.
            '</div>';
        }
    }

    /**
     *
     * @return string|null
     */
    protected function getModule()
    {
        $module = null;
        if (!empty($this->model->context)) {
            try {
                $modelContext = \Yii::createObject($this->model->context);
                if (method_exists($modelContext, 'getPluginModule')) {
                    $module = $modelContext->getPluginModule();
                } else {
                    $modelArray = explode('\\', $this->model->context);
                    if (!empty(\Yii::$app->getModule('dashboard')->modulesSubdashboard)) {
                        $modules = \Yii::$app->getModule('dashboard')->modulesSubdashboard;
                        $module  = $this->getModuleByModel($modelArray, $modules);
                    } else {
                        $module = $this->getModuleByApp($modelArray, $modules);
                    }
                }
            } catch (\Exception $e) {
                $modelArray = explode('\\', $this->model->context);
                $module     = $this->getModuleByModel($modelArray);
            }
        }
        return $module;
    }

    /**
     * 
     * @param array $arr_model
     * @param array $modules
     * @return string|null
     */
    protected function getModuleByModel($arr_model, $modules)
    {
        $module = null;
        foreach ($modules as $k => $v) {
            if (in_array($v, $arr_model)) {
                $module = $v;
                continue;
            }
        }
        return $module;
    }

    /**
     *
     * @param array $arr_model
     * @return string|null
     */
    protected function getModuleByApp($arr_model)
    {
        $module  = null;
        $modules = \Yii::$app->getModules(false);
        foreach ($modules as $k => $v) {
            if (in_array($k, $arr_model)) {
                $module = $k;
                continue;
            }
        }
        return $module;
    }
}