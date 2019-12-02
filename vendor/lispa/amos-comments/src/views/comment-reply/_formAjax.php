<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\comments\views\comment-reply
 * @category   CategoryName
 */

/**
 * @var yii\web\View $this
 * @var lispa\amos\comments\models\CommentReply $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<?= $this->render('_form', [
    'model' => $model,
    'fid' => (NULL !== (filter_input(INPUT_GET, 'fid'))) ? filter_input(INPUT_GET, 'fid') : '',
    'dataField' => (NULL !== (filter_input(INPUT_GET, 'dataField'))) ? filter_input(INPUT_GET, 'dataField') : '',
    'dataEntity' => (NULL !== (filter_input(INPUT_GET, 'dataEntity'))) ? filter_input(INPUT_GET, 'dataEntity') : '',
    'class' => 'dynamicCreation'
]) ?>
