<?php

use app\models\forms\RegistrationForm;
use app\services\Route;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

/** @var $mForm RegistrationForm */

$sRulesUrl = Url::toRoute([Route::ABOUT]);
?>
<?php $form = ActiveForm::begin([
    'id' => 'form-registration',
    'action' => Url::toRoute(Route::REGISTRATION)
]) ?>
<?= $form->field($mForm, 'email')->input('email') ?>
<?= $form->field($mForm, 'password')->passwordInput() ?>
<?= $form->field($mForm, 'password_repeat')->passwordInput() ?>
<?= $form->field($mForm, 'agree')->checkbox([
    'label' => 'Принимаю условия <a href="' . $sRulesUrl . '" target="_blank">пользовательского соглашения</a>'
]) ?>

<div class="text-center mt-3">
    <button type="submit" class="btn btn-success btn-lg">Зарегистрироваться</button>
</div>
<?php ActiveForm::end() ?>