<?php

use app\services\DateFormat;
use app\services\Route;
use app\services\WebUser;
use yii\helpers\Html;
use app\services\Document as DocumentService;
use yii\helpers\Url;

/**
 * Vars helper. Under the cut
 *
 * @var yii\web\View $this
 * @var app\models\Document $mDocument
 */

$this->title = 'Документ "' . $mDocument->getName() . '"';
?>
<?php if(!$mDocument->isPublic()) { ?>
    <div class="alert alert-info mt-2" role="alert">
        Это приватный документ. Доступ к нему есть только у тех, кто знает специальную ссылку!<br>
        Специальная ссылка: <strong><?= DocumentService::getViewUrl($mDocument, true) ?></strong>
    </div>
<?php } ?>
<h1 class="text-center mt-5 mb-2"><?= Html::encode($mDocument->getName()) ?></h1>
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card card-view-document">
            <img src="<?= DocumentService::getPreviewUrl($mDocument) ?>" class="card-img-top" alt="<?= Html::encode($mDocument->getFilename()) ?>">
            <div class="card-body">
                <p><?= nl2br(Html::encode($mDocument->getKeywords())) ?></p>
                <p class="text-end">
                    <small class="text-muted">
                        <?= DateFormat::datetimeShort($mDocument->getCreated()) ?>,<br>
                        <strong><?= Html::encode($mDocument->getOwner()->getSiteName()) ?></strong>
                    </small>
                </p>
            </div>
            <div class="card-footer text-center">
                <a class="btn btn-success" href="<?= DocumentService::getDownloadUrl($mDocument) ?>">
                    Скачать
                </a>
                <?php if ($mDocument->isOwner(WebUser::getAuthUser())) { ?>
                    <a class="btn btn-primary" href="<?= Url::toRoute([Route::DOCUMENT_UPDATE, 'iDocumentId' => $mDocument->getId()]) ?>">Изменить</a>
                    <a class="btn btn-danger" href="<?= Url::toRoute([Route::DOCUMENT_DELETE, 'iDocumentId' => $mDocument->getId()]) ?>"
                       data-confirm="Точно удалить?" data-method="post">Удалить</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
