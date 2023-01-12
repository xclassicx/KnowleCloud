<?php

use app\services\Route;
use app\services\WebUser;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Vars helper. Under the cut
 *
 * @var \yii\web\View $this
 */

$mAuthUser = WebUser::getAuthUser();
?>
<nav class="navbar fixed-top navbar-expand-lg bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?= Url::toRoute([Route::ROOT]) ?>"><?= Html::encode(Yii::$app->name) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">

            <?php /** Тут проще вручную форму собрать, чем юзать ActiveForm */ ?>
            <form class="me-auto" role="search" action="<?= Url::toRoute([Route::DOCUMENT_SEARCH]) ?>">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Что ищем?" required>
                    <button class="btn btn-outline-success" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>

            <ul class="d-flex ms-auto navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::toRoute(Route::ABOUT) ?>">О проекте</a>
                </li>
                <?php if($mAuthUser === null) { ?>
                <li class="nav-item"><a class="nav-link" href="<?= Url::toRoute(Route::LOGIN) ?>">Войти</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= Url::toRoute(Route::REGISTRATION) ?>">Зарегистрироваться</a></li>
                <?php } else { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <?= Html::encode($mAuthUser->getSiteName()) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?= Url::toRoute(Route::DOCUMENT_CREATE) ?>">
                                <i class="fa fa-cloud-upload"></i>&nbsp;Загрузить&nbsp;документ
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= Url::toRoute(Route::DOCUMENT_MY) ?>">
                                <i class="fa fa-th"></i>&nbsp;Мои&nbsp;документы
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= Url::toRoute(Route::LOGOUT) ?>" data-method="post">
                                <i class="fa fa-sign-out"></i>&nbsp;Выйти
                            </a>
                        </li>
                    </ul>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>