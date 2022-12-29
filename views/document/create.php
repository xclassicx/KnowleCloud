<?php

use yii\helpers\Html;

/**
 * Vars helper. Under the cut
 *
 * @var \yii\web\View $this
 * @var app\models\Document $mDocument
 */

$this->title = 'Создать документ';
?>
<div class="row justify-content-center mt-5">
    <div class="col-12 col-lg-offset-2 col-lg-8 col-xl-offset-3 col-xl-6">
        <h1 class="m-b-3 text-center"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_form', [
            'mDocument' => $mDocument,
        ]) ?>
    </div>
</div>

