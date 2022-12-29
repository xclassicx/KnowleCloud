<?php

namespace app\controllers;

use app\models\Document;
use yii\data\ArrayDataProvider;
use yii\web\Controller;

class SiteController extends Controller
{
    const NAME = 'site';
    const MAIN_PAGE_DOC_LIMIT = 24;

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @see \app\services\Route::ROOT
     */
    public function actionIndex(): string
    {
        $aDocuments = Document::find()
            ->forCatalog()
            ->orderBy('id DESC')
            ->limit(self::MAIN_PAGE_DOC_LIMIT)
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels'  => $aDocuments,
            'pagination' => [
                'pageSize' => self::MAIN_PAGE_DOC_LIMIT,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @see \app\services\Route::ABOUT
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }
}
