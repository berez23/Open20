<?php
use lispa\amos\core\utilities\ViewUtility;
use lispa\amos\sondaggi\AmosSondaggi;

use kartik\detail\DetailView;

/**
 * @var yii\web\View $this
 * @var lispa\amos\sondaggi\models\SondaggiRisposteSessioni $model
 */

$this->title = $model;
$this->params['breadcrumbs'][] = ['label' => AmosSondaggi::t('amossondaggi', 'Sondaggi Risposte Sessioni'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sondaggi-risposte-sessioni-view">
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'session_id',
        'unique_id',
        ['attribute' => 'begin_date', 'format' => ['datetime', ViewUtility::formatDateTime()]],
        ['attribute' => 'end_date', 'format' => ['datetime', ViewUtility::formatDateTime()]],
        'session_tmp:ntext',
        'user_profile_id',
        'sondaggi_id',
        ['attribute' => 'created_at', 'format' => ['datetime', ViewUtility::formatDateTime()]],
        ['attribute' => 'updated_at', 'format' => ['datetime', ViewUtility::formatDateTime()]],
        ['attribute' => 'deleted_at', 'format' => ['datetime', ViewUtility::formatDateTime()]],
        'created_by',
        'updated_by',
        'deleted_by',
        'version',
    ],
]) ?>
</div>
