<?php

use app\models\forms\RegistrationForm;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $mForm RegistrationForm */

$this->title = 'Регистрация';
?>
<div class="row justify-content-center mt-5">
    <div class="col-12 col-lg-offset-2 col-lg-8 col-xl-offset-3 col-xl-6">
        <h1 class="m-b-3 text-center"><?= Html::encode($this->title) ?></h1>
        <?= $this->render('_registration-form', ['mForm' => $mForm]) ?>
    </div>
</div>