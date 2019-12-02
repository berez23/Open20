<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni\views\profilo
 * @category   CategoryName
 */

use lispa\amos\core\forms\editors\m2mWidget\M2MWidget;
use lispa\amos\organizzazioni\models\Profilo;
use yii\db\ActiveQuery;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\projectmanagement\models\Projects $model
 */

$this->title = $model;
$this->params['breadcrumbs'][] = ['label' => \lispa\amos\projectmanagement\Module::t('amosproject_management', 'Projects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => strip_tags($model),
    'url' => ['update', 'id' => $model->id, '#' => 'tab-organizations']
];
$this->params['breadcrumbs'][] = \lispa\amos\projectmanagement\Module::t('amosproject_management', 'Invite Organizations');

/** @var Profilo $organizationModelClass */
$organizationModelClass = \lispa\amos\organizzazioni\Module::instance()->createModel('Profilo')->className();

/** @var ActiveQuery $query */
$query = $organizationModelClass::find()->andFilterWhere([
    'not in',
    'id',
    $model->getProjectsJoinedOrganizationsMms()->select('organization_id')
])->orderBy('name');
$post = Yii::$app->request->post();
if (isset($post['genericSearch'])) {
    $query->andFilterWhere([
        'or',
        ['like', $organizationModelClass::tableName() . '.name', $post['genericSearch']],
        ['like', $organizationModelClass::tableName() . '.indirizzo', $post['genericSearch']],
        ['like', $organizationModelClass::tableName() . '.partita_iva', $post['genericSearch']],
        ['like', $organizationModelClass::tableName() . '.codice_fiscale', $post['genericSearch']],
    ]);
}

?>
<?= M2MWidget::widget([
    'model' => $model,
    'modelId' => $model->id,
    'modelData' => $model->getJoinedOrganizations(),
    'modelDataArrFromTo' => [
        'from' => 'id',
        'to' => 'id'
    ],
    'modelTargetSearch' => [
        'class' => $organizationModelClass::className(),
        'query' => $query
    ],
    'viewSearch' => (isset($viewM2MWidgetGenericSearch) ? $viewM2MWidgetGenericSearch : false),
    'targetUrlController' => 'projects',
    'moduleClassName' => \lispa\amos\projectmanagement\Module::className(),
    'postName' => 'Project',
    'postKey' => 'Organization',
    'targetColumnsToView' => [
        'name' => [
            'attribute' => 'name',
            'label' => \lispa\amos\projectmanagement\Module::t('amosproject_management', 'Name'),
            'headerOptions' => [
                'id' => \lispa\amos\projectmanagement\Module::t('amosproject_management', 'Name'),
            ],
            'contentOptions' => [
                'headers' => \lispa\amos\projectmanagement\Module::t('amosproject_management', 'Name'),
            ]
        ],
        'indirizzo',
    ],
]);
?>
