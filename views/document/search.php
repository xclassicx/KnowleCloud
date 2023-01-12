<?php

use app\widgets\GridView;
use yii\bootstrap5\LinkPager;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * Vars helper. Under the cut
 *
 * @var yii\web\View $this
 * @var ActiveDataProvider $dataProvider
 * @var \app\models\forms\Search $mSearch
 */

$this->title = 'Результат поиска';
?>

    <h1 class="mt-5 mb-5 text-center">
        <?= Html::encode($this->title) ?>
        <?= $mSearch->q ? Html::encode(' "' . $mSearch->q . '"') : '' ?>
    </h1>

<?php foreach ($mSearch->getFirstErrors() as $error) { ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php } ?>

<?php if ($dataProvider !== false) { ?>
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
<?php } ?>