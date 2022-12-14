<?php

use app\models\forms\LoginForm;
use app\services\Route;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

/** @var LoginForm $mForm  */
/** @var \yii\web\View $this */
?>
<?php $form = ActiveForm::begin([
    'id' => 'form-login',
    'action' => Url::toRoute(Route::LOGIN)
]) ?>

<?= $form->field($mForm, 'email')->input('email') ?>
<?= $form->field($mForm, 'password')->passwordInput() ?>
<?= $form->field($mForm, 'rememberMe')->checkbox() ?>

<div class="text-center mt-3">
    <button type="submit" class="btn btn-success btn-lg">Войти</button>
    <a class="btn btn-outline-success" href="<?= Url::toRoute([Route::REGISTRATION]) ?>">
        Регистрация
    </a>
</div>
<?php ActiveForm::end() ?>
