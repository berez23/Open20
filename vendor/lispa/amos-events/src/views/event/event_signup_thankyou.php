<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\events\views\event
 * @category   CategoryName
 */

use lispa\amos\core\forms\CloseButtonWidget;
use lispa\amos\events\AmosEvents;

/**
 * @var yii\web\View $this
 * @var \lispa\amos\events\models\Event $event
 */

$this->title = AmosEvents::txt('#event_signup_thankyou_success');
$this->params['breadcrumbs'][] = $this->title;

?>

<h3><?= AmosEvents::txt('Grazie di esserti registrato.'); ?></h3>
<h3><?= AmosEvents::txt('Abbiamo inviato una email di conferma e riepilogo.'); ?></h3>
<h3><?= AmosEvents::txt('Arrivederci a') . ' ' . $event->title; ?></h3>
<div class="btnViewContainer pull-left">
    <?= CloseButtonWidget::widget([
        'title' => AmosEvents::t('amosevents', '#go_back_to_event'),
        'layoutClass' => 'pull-left',
        'urlClose' => $event->getFullViewUrl()
    ]); ?>
</div>
