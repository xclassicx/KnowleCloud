<?php

use app\services\Route;
use app\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Vars helper. Under the cut
 *
 * @var yii\web\View $this
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Мои документы';
?>

<h1 class="mt-5"><?= Html::encode($this->title) ?></h1>
<p>
    <a class="btn btn-success" href="<?= Url::toRoute(Route::DOCUMENT_CREATE) ?>">Загрузить документ</a>
</p>

<?php try { ?>
    <?= GridView::widget([
        'dataProvider'   => $dataProvider,
        'emptyText'      => '<p class="text-center">Документы не найдены</p>',
        'layout'         => $this->render('@app/views/_gridViewLayout'),
        'itemView'       => '_list-item',
        'viewParams'     => ['itemCssClass' => 'col-3'],
        'sModelViewName' => 'mDocument',
    ]) ?>
    <?php
} catch (Throwable $ex) {
    if (YII_ENV_DEV) {
        throw $ex;
    } else {
        echo 'Не осилил собрать виджет';
        Yii::error($ex);
    }
}
?>
