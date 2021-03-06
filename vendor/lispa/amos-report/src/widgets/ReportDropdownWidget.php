<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    amos-report
 * @category   Widget
 */

namespace lispa\amos\report\widgets;

use lispa\amos\core\helpers\Html;
use lispa\amos\core\icons\AmosIcons;
use lispa\amos\core\utilities\ModalUtility;
use lispa\amos\core\module\BaseAmosModule;
use lispa\amos\core\widget\WidgetAbstract;
use lispa\amos\core\record\Record;
use lispa\amos\report\AmosReport;
use lispa\amos\report\models\Report;
use lispa\amos\report\utilities\ReportUtil;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Dropdown;
use yii\data\ActiveDataProvider;

/**
 * Class ReportDropdownWidget
 * @package lispa\amos\report\widgets
 */
class ReportDropdownWidget extends Widget
{
    /**
     * @var string $modelClassName the current model processed className
     */
    public $modelClassName = '';

    /**
     * @var Record
     */
    public $model = null;

    /**
     * @var null
     */
    public $context_id = null;

    /**
     * @var string
     */
    public $layout = "{reportButton}";
    /**
     * @var array
     */
    public $renderSections = [];

    /**
     * @var string
     */
    public $title = '';

    public $renderListModalWidget = false;

    public $permissionName = null;

    private $hasPermission = false;

    /**
     * widget initialization
     */
    public function init()
    {
        parent::init();

        if (is_null($this->model)) {
            throw new \Exception(BaseAmosModule::t('amosreport', 'Missing Model'));
        } else {
            $this->modelClassName = $this->model->classname();
            $this->context_id = $this->model->id;
        }

        if (!is_null($this->permissionName)) {
            $this->hasPermission = Yii::$app->user->can($this->permissionName, ['model' => $this->model]);
        } else {
            $this->hasPermission = (Yii::$app->user->can(strtoupper($this->model->formName() . '_UPDATE'), ['model' => $this->model])
                || \Yii::$app->user->can($this->modelClassName . '_UPDATE', ['model' => $this->model]));
        }

    }


    /**
     * @return mixed
     */
    public function run()
    {
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->layout);

        $reportsListModalWidget = '';
        if ($this->renderListModalWidget && $this->hasPermission) {
            $reportsListModalWidget = ReportsListModalWidget::widget([
                'model' => $this->model
            ]);
        }

        echo $content;

        $modalContentWarning = $reportsListModalWidget . $this->render('report', [
                'widget' => $this,
                'context_id' => $this->context_id,
                'className' => $this->modelClassName,
                'title' => $this->title
            ]);

        $modalCreateReport = ModalUtility::amosModal([
            'id' => 'modal_report-' . $this->context_id,
            'headerClass' => 'modal-utility-warning',
            'headerText' => AmosIcons::show('flag') . AmosReport::t('amosreport', '#reports_content_list_modal-title'),
            'modalBodyContent' => $modalContentWarning,
            'modalClassSize' => 'modal-lg'
        ]);

        $model = $this->model;
        /** @var ActiveQuery $query */
        $query = Report::find()->andWhere([
            'classname' => $model->className(),
            'context_id' => $model->id
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => $limit,
//            ]
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_ASC,
                    'created_at' => SORT_ASC
                ]
            ]
        ]);

        $modalContentList = $this->render('modal-reports-list', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'context_id' => $model->id,
        ]);

        $modalListReport = ModalUtility::amosModal([
            'id' => 'modal_reports_list-' . $model->id,
            'headerClass' => 'modal-utility-confirm',
            'headerText' => AmosIcons::show('flag') . AmosReport::t('amosreport', '#reports_content_list_modal-title'),
            'modalBodyContent' => $modalContentList,
            'modalClassSize' => 'modal-lg'
        ]);

        return $modalCreateReport . $modalListReport;
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{publisher}`, `{publisherAdv}`.
     * @return string|bool the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        if (isset($this->renderSections[$name]) && $this->renderSections[$name] instanceof \Closure) {
            return call_user_func($this->renderSections[$name], $this->model, $this);
        }
        switch ($name) {
            case '{reportButton}':
                return $this->renderReportDropdown();
            default:
                return false;
        }
    }

    /**
     * @return string
     */
    public function renderReportDropdown()
    {

        $items[] = [
            'label' => BaseAmosModule::t('amosreport', '#create_report'),
            'url' => '#',
            'options' => [
                'id' => 'load_form-' . $this->context_id,
                'title' => AmosReport::t('amosreport', 'You can report errors or contents that you consider inappropriate and, if necessary, ask for correction')
            ]
        ];

        if ($this->hasPermission) {
            $items[] = [
                'label' => BaseAmosModule::t('amosreport', '#show_reports'),
                'url' => '#',
                'options' => [
                    'id' => 'load_reports_list-' . $this->context_id,
                    'title' => AmosReport::t('amosreport', '#view_reports_list')
                ]
            ];
        }

        $dropDown = Dropdown::widget([
            'items' => $items,
        ]);

        $reportsCount = ReportUtil::retrieveReportsCount($this->modelClassName, $this->context_id);

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $icon = ($this->modelClassName == 'lispa\amos\community\models\Community') ? AmosIcons::show('segnalazioni', '', AmosIcons::IC) : AmosIcons::show('flag');
        } else {
            $icon = AmosIcons::show('flag');
        }

        $button = Html::tag('div',
            Html::a(
                $icon .
                Html::tag('span', $reportsCount, ['class' => 'counter']) .
                Html::tag('b', '', ['class' => 'caret']),
                '#',
                [
                    'data-toggle' => 'dropdown',
                    'class' => 'btn dropdown-toggle'
                ]
            ) . $dropDown
            ,
            ['class' => 'dropdown report-dropdown']
        );

        return $button;
    }


}