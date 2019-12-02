<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\core
 * @category   CategoryName
 */

use lispa\amos\core\components\AmosView;
use yii\helpers\Html;
use yii\helpers\Url;
use lispa\amos\core\widget\WidgetAbstract;

////\bedezign\yii2\audit\web\JSLoggingAsset::register($this);

/** @var $this \lispa\amos\core\components\AmosView */
/** @var $content string */

$urlCorrente = Url::current();
$arrayUrl = explode('/', $urlCorrente);
$countArrayUrl = count($arrayUrl);
$percorso = '';
$i = 0;
$moduloId = Yii::$app->controller->module->id;
$basePath = Yii::$app->getBasePath();
if ($moduloId != 'app-backend' && $moduloId != 'app-frontend') {
    $basePath = \Yii::$app->getModule($moduloId)->getBasePath();
    $percorso .= '/modules/' . $moduloId . '/views/' . $arrayUrl[$countArrayUrl - 2];
} else {
    $percorso .= 'views';
    while ($i < ($countArrayUrl - 1)) {
        $percorso .= $arrayUrl[$i] . '/';
        $i++;
    }
}
if ($countArrayUrl) {
    $posizioneEsclusione = strpos($arrayUrl[$countArrayUrl - 1], '?');
    if ($posizioneEsclusione > 0) {
        $vista = substr($arrayUrl[$countArrayUrl - 1], 0, $posizioneEsclusione);
    } else {
        $vista = $arrayUrl[$countArrayUrl - 1];
    }
    if (file_exists($basePath . '/' . $percorso . '/help/' . $vista . '.php')) {
        $this->params['help'] = [
            'filename' => $vista
        ];
    }
}
?>


<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "head"); ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "header"); ?>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "logo"); ?>

    <?php if (isset(Yii::$app->params['logo-bordo'])): ?>
        <div class="container-bordo-logo"><img src="<?= Yii::$app->params['logo-bordo'] ?>" alt=""></div>
    <?php endif; ?>

    <section id="bk-page" class="community-page">

        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "messages"); ?>

        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "help"); ?>

        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "network_scope"); ?>

        <?= $this->render("parts" . DIRECTORY_SEPARATOR . "breadcrumb"); ?>

        <div class="page-content-noMargin">
            <!--                <div class="page-header">-->
<!--                    --><?php //if (!is_null($this->title)): ?>
<!--                        <h1 class="title">< ?= Html::encode($this->title) ?></h1>-->
<!--                        < ?= $this->render("parts" . DIRECTORY_SEPARATOR . "textHelp"); ?>-->
<!--                    --><?php //endif; ?>
<!--                </div>-->

            <?php if ($this instanceof AmosView): ?>
                <?php $this->beginViewContent() ?>
            <?php endif; ?>
            <?= $content ?>
            <?php if ($this instanceof AmosView): ?>
                <?php $this->endViewContent() ?>
            <?php endif; ?>
        </div>

    </section>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "sponsors"); ?>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "footer_text"); ?>

    <?= $this->render("parts" . DIRECTORY_SEPARATOR . "assistance"); ?>


    <?php $this->endBody() ?>

    </body>
    </html>
<?php $this->endPage() ?>