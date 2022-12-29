<?php

use app\models\Document;
use app\services\Document as DocumentService;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/**
 * Vars helper. Under the cut
 *
 * @var yii\web\View $this
 * @var app\models\Document $mDocument
 * @var yii\widgets\ActiveForm $form
 */

// Эта view используется и для создания нового document и для редактирования существующего.
// Для нового документа необходимо инициализировать те булевы значения, которые должны быть ture
if ($mDocument->isNewRecord) {
    $mDocument->makePublic();
}
?>

<?php $form = ActiveForm::begin(); ?>

<?php if ($mDocument->isNewRecord) { ?>
    <?php /* Не даем менять сам файл при редактировании, ибо хз зачем - поле file показываем только при создании document */ ?>
    <?= $form->field($mDocument, 'uploadedFile')
        ->fileInput()
        ->hint(
            'Допустимые типы файлов: ' . implode(' ,', Document::AVAILABLE_EXTENSIONS) . '<br>' .
            'Максимальный размер файла ' . Document::MAX_SIZE_MB . 'Мб'
        ) ?>
<?php } else { ?>
    <?php /* При редактировании просто показываем картинку превью */ ?>
    <p class="text-center">
        <img src="<?= DocumentService::getPreviewUrl($mDocument) ?>" class="img-fluid rounded" alt="<?= Html::encode($mDocument->getFilename()) ?>">
    </p>
<?php } ?>

<?= $form->field($mDocument, 'name', ['validateOnType' => true, 'validationDelay' => 0])
    ->textInput(['maxlength' => true])
    ->hint('Что содержится в файле? Это поле имеет больший вес при поиске')
?>
<?= $form->field($mDocument, 'keywords', ['validateOnType' => true, 'validationDelay' => 0])
    ->textarea(['rows' => 6])
    ->hint('Описание документа для поиска: учебное заведение, курс, дисциплина, год, преподаватель итд') ?>

<?= $form->field($mDocument, 'public')
    ->checkbox()
    ->hint('Если галочка снята, скачивание документа возможно только по специальной ссылке') ?>

<div class="text-center mt-3">
    <button type="submit" class="btn btn-success btn-lg">
        <?= $mDocument->isNewRecord ? 'Создать' : 'Сохранить' ?>
    </button>
</div>
<?php ActiveForm::end(); ?>


