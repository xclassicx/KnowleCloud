<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\services\WebUser;
use yii\base\Action;
use yii\base\Controller;
use yii\base\Module;
use yii\bootstrap5\Alert;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header style="height: 56px;">
<?= $this->render('_navbar') ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?php /** FLASH MESSANGER  */
        foreach (Yii::$app->session->getAllFlashes() as $sKey => $aMessages) {
            try {
                echo Alert::widget([
                    'options' => [
                        'class' => 'alert-' . $sKey,
                        'style' => 'margin-bottom: 0;'
                    ],
                    'body'    => implode('<br>', $aMessages),
                ]);
            } catch (Exception $ex) {
                Yii::error($ex);
                echo 'Ошибка формирования алерта';
            }
        }
        /** FLASH MESSANGER END */ ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p class="float-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
    </div>
</footer>

<?php if(YII_ENV_DEV) : ?>
    <?php $iUserID = WebUser::getAuthUser() ? WebUser::getAuthUser()->getId() : 0; ?>
    <style>
        .badge.system {
            left: 10px;
            opacity: 0.7;
            position: fixed;
        }
        .badge.system:hover {
            opacity: 1;
        }
    </style>
    <span class="system badge bg-danger" style="bottom: 210px; font-size: 16px;"><?= YII_ENV ?></span>
    <span class="system badge text-dark bg-light" style="bottom: 185px;">iUserID:<?= $iUserID ?></span>

    <?php if($this->context instanceof Controller) { ?>
        <?php
        $sModule = '';
        if($this->context->module instanceof Module) {
            $sModule = $this->context->module->id;
        }

        $sControllerId = $this->context->id;

        $sActionId = '';
        if($this->context->action instanceof Action) {
            $sActionId = $this->context->action->id;
        }
        ?>
        <span class="system badge text-bg-info" style="bottom: 110px;">module: <?= $sModule ?></span>
        <span class="system badge text-bg-primary" style="bottom: 85px;">controller: <?= $sControllerId ?></span>
        <span class="system badge text-bg-success" style="bottom: 60px;">action: <?= $sActionId ?></span>
    <?php } ?>

    <span class="d-block d-sm-none system badge rounded-pill text-bg-danger" style="bottom: 145px; font-size: 16px;">XS</span>
    <span class="d-none d-sm-block d-md-none system badge rounded-pill text-bg-warning" style="bottom: 145px; font-size: 16px;">SM</span>
    <span class="d-none d-md-block d-lg-none system badge rounded-pill text-bg-info" style="bottom: 145px; font-size: 16px;">MD</span>
    <span class="d-none d-lg-block d-xl-none system badge rounded-pill text-bg-primary" style="bottom: 145px; font-size: 16px;">LG</span>
    <span class="d-none d-xl-block d-xxl-none system badge rounded-pill text-bg-success" style="bottom: 145px; font-size: 16px;">XL</span>
    <span class="d-none d-xxl-block system badge rounded-pill text-bg-dark" style="bottom: 145px; font-size: 16px;">XXL</span>
<?php endif; ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
