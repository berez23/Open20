<?php

use lispa\amos\core\forms\ActiveForm;
use lispa\amos\core\forms\TextEditorWidget;
use lispa\amos\core\helpers\Html;
use lispa\amos\projectmanagement\models\ProjectsProgressStatements;
use lispa\amos\projectmanagement\models\ProjectsProgressStatementsType;
use lispa\amos\projectmanagement\Module;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var lispa\amos\projectmanagement\models\ProjectsProgressStatements $model
 * @var yii\widgets\ActiveForm $form
 */
$task = $model->projectsTask;
$canEdit = \lispa\amos\projectmanagement\utility\ProjectManagementUtility::isLoggedUserResponsibleTaskOrActivity($task);
$upRef = \lispa\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $task->getUserResponsibleId()])->one();

$moduleL = \Yii::$app->getModule('layout');
if (!empty($moduleL)) {
    \lispa\amos\projectmanagement\assets\AmosProjectManagementAsset::register($this);
}
?>

<?php $form = ActiveForm::begin(); ?>
<?php
try {
    echo \lispa\amos\workflow\widgets\WorkflowTransitionStateDescriptorWidget::widget([
        'form' => $form,
        'model' => $model,
        'workflowId' => \lispa\amos\projectmanagement\models\ProjectsProgressStatements::PROJECTSACTIVITYPROGRESS_WORKFLOW,
        'classDivIcon' => '',
        'classDivMessage' => 'message',
        'viewWidgetOnNewRecord' => true
    ]);
} catch (Exception $e) {
    pr($e->getMessage() . ' L:' . $e->getLine(), 'ERROR');
}
?>
    <div class="projects-progress-statements-form col-xs-12 nop">

        <div class="row">
            <div class="col-xs-12">
                <h2 class="subtitle-form"><?= Module::t('amosproject_management', '#project_progress_statements_activity') ?></h2>
            </div>
        </div>

        <div class="col-xs-12 nop">
            <div class="col-xs-12 info-project-progress-statements-area nop">
                <div class="col-xs-12 nop info-body">
                    <div class="col-lg-4 col-xs-12 nop">
                        <div class="col-xs-3 nopl info-label"><?= $task->getAttributeLabel('name') ?></div>
                        <div class="col-xs-9 nopl info-value"><?= $task->name ?></div>
                    </div>
                    <div class="col-lg-4 col-xs-12 nop">
                        <div class="col-xs-3 nopl info-label"><?= Module::t('amosproject_management', 'Responsible') ?></div>
                        <div class="col-xs-9 nopl info-value"><?= $upRef->getNomeCognome() ?></div>
                    </div>
                    <div class="col-xs-12 nop">
                        <div class="col-xs-1 nop info-label"><p><?= Module::t('amosproject_management', 'Description') ?></p></div>
                        <div class="col-xs-11 nop info-value"><?= $task->description ?></div>
                    </div>
                    <div class="col-lg-4 col-xs-12 nop">
                        <div class="col-xs-3 nopl info-label"><?= Module::t('amosproject_management', 'Start Date') ?></div>
                        <div class="col-xs-9 nopl info-value"><?= Yii::$app->formatter->asDate($task->start_date) ?></div>
                    </div>
                    <div class="col-lg-4 col-xs-12 nop">
                        <div class="col-xs-3 nopl info-label"><?= Module::t('amosproject_management', 'End Date') ?></div>
                        <div class="col-xs-9 nopl info-value"><?= Yii::$app->formatter->asDate($task->end_date) ?></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-8 col-xs-12 nop">
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'projects_progress_statements_type_id')->widget(Select2::className(), [
                'data' => \yii\helpers\ArrayHelper::map(ProjectsProgressStatementsType::find()->all(), 'id', 'nameTranslate'),
                'options' =>
                    [
                        'placeholder' => Module::t('amosproject_management', 'Select...'),
                        'disabled' => !$canEdit
                    ]
            ]);
            ?>
        </div>
        <?php if ($canEdit) : ?>
            <div class="col-lg-8 col-xs-12 nop">
                <?= $form->field($model, 'note')->widget(TextEditorWidget::className(), [
                    'clientOptions' => [
                        'placeholder' => Module::t('amosproject_management', 'Note'),
                        'lang' => substr(Yii::$app->language, 0, 2)
                    ]
                ]) ?>
            </div>
        <?php else : ?>
            <div class="m-b-15">
                <?= $form->field($model, 'note')->hiddenInput([]) ?>
                <?= $model->note ?>
            </div>
        <?php endif; ?>

        <div class="col-xs-12 note_asterisk">
            <span><?= Module::t('amosproject_management', '#required_field') ?></span>
        </div>

        <div class="clearfix"></div>

        <?php
        $draftButtonsDefault = [
            'button' => $model->isNewRecord ? Html::submitButton(Module::t('amosproject_management', 'Crea'), ['class' => 'btn btn-workflow']) : Html::submitButton(Module::t('amosproject_management', 'Salva'), ['class' => 'btn btn-workflow']),
            'description' => Yii::t('amosnews', 'lo stato di avanzamento'),
        ];
        try {
            echo \lispa\amos\workflow\widgets\WorkflowTransitionButtonsWidget::widget([
                'form' => $form,
                'model' => $model,
                'workflowId' => ProjectsProgressStatements::PROJECTSACTIVITYPROGRESS_WORKFLOW,
                'viewWidgetOnNewRecord' => true,

                'closeButton' => Html::a(Module::t('amosproject_management', 'Annulla'), Yii::$app->session->get('previousUrl') . '#statements', ['class' => 'btn btn-secondary']),

                // fisso lo stato iniziale per generazione pulsanti e comportamenti
                // "fake" in fase di creazione (il record non e' ancora inserito nel db)
                'initialStatusName' => "ONEDIT",
                'initialStatus' => ProjectsProgressStatements::PROJECTSACTIVITYPROGRESS_WORKFLOW_STATUS_ONEDIT,

                // Setto e renderizzo il pulsante di salvataggio (quello che fa rimanere
                // nello stato in cui mi trovo) in modo diverso in base allo stato in cui mi trovo
                // in modo tale da creare descrizioni e label dei bottoni ad hoc in base allo stato
                // del workflow in cui mi trovo.

                // Il caso 'default' è obbligatorio e definisce il bottone "di default" da visualizzare
                // sempre con la sua description, l'array con chiave status (vedi esempio sotto) viene
                // richiamato solo quando il contenuto si trova nello stato definito esplicitamente

                'draftButtons' => [
                    'default' => ($model->status == ProjectsProgressStatements::PROJECTSACTIVITYPROGRESS_WORKFLOW_STATUS_ONEDIT) ? $draftButtonsDefault : null
                ]
            ]);
        } catch (Exception $e) {
            pr($e->getMessage());
        }
        ?>

    </div>
<?php ActiveForm::end(); ?>