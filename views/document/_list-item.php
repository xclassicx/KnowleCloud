<?php

use app\models\Document;
use app\models\elasticsearch\Document as ElasticsearchDocument;
use app\services\DateFormat;
use app\services\Document as DocumentService;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * Vars helper. Under the cut
 *
 * @var \yii\web\View $this
 * @var ?string $itemCssClass
 * @var \app\models\Document $mDocument
 */

// учим вьюшку работать с результатами поиска
if ($mDocument instanceof ElasticsearchDocument) {
    $mDocument = Document::findOne($mDocument->id);
}

// Обрезаем имя document до 4 слов. Либо до 45 символов, если это 4 длинных слова
$sTruncName = StringHelper::truncate(StringHelper::truncateWords($mDocument->getName(), 4), 45);
// Тк каждая из обрезалок(и по словам и по символам) может поставить "..." , то в конце заголовка может получиться аж ".....". Исправляем:
$sTruncName = preg_replace("/\.{3,}$/im", "...", $sTruncName);
?>
<div class="<?= Html::encode($itemCssClass) ?> mb-3">
    <div class="card card-document" <?= $mDocument->isPublic() ? '' : 'style="border: 1px solid #b6effb; background-color: #cff4fc;"' ?>>
        <a href="<?= DocumentService::getViewUrl($mDocument) ?>">
            <img src="<?= DocumentService::getPreviewUrl($mDocument) ?>" class="card-img-top"
                 alt="<?= Html::encode($mDocument->getFilename()) ?>">
            <div class="card-body">
                <h4 class="card-title">
                    <?= $mDocument->isPublic() ? '' : '<i class="fa fa-lock"></i> ' ?>
                    <?= nl2br(Html::encode($sTruncName)) ?>
                </h4>
                <p class="text-end mb-0">
                    <small class="text-muted">
                        <i class="fa fa-clock-o"></i> <time><?= DateFormat::datetimeShort($mDocument->getCreated()) ?></time><br>
                        <i class="fa fa-user"></i> <strong><?= Html::encode($mDocument->getOwner()->getSiteName()) ?></strong>
                    </small>
                </p>
            </div>
        </a>
    </div>
</div>