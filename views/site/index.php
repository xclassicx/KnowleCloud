<?php

use app\widgets\GridView;

/**
 * Vars helper. Under the cut
 *
 * @var \yii\web\View $this
 * @var \yii\data\ArrayDataProvider $dataProvider
 */

$this->title = Yii::$app->name;
?>
<?php if ($dataProvider->getCount()) { ?>
    <h1 class="mt-5">Последние документы</h1>
    <?php try { ?>
        <?= GridView::widget([
            'dataProvider'   => $dataProvider,
            'emptyText'      => '<p class="text-center">Документы не найдены</p>',
            'layout'         => $this->render('@app/views/_gridViewLayout'),
            'itemView'       => '@app/views/document/_list-item',
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