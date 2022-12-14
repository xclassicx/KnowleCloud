<?php

use app\models\forms\LoginForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var LoginForm $mForm  */

$this->title = 'Вход';
?>
<div class="row justify-content-center mt-5">
    <div class="col-12 col-lg-offset-2 col-lg-8 col-xl-offset-3 col-xl-6">
        <h1 class="m-b-3 text-center"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_login-form', ['mForm' => $mForm]) ?>
    </div>
</div>