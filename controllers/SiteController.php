<?php

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    const NAME = 'site';

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
        return $this->render('index');
    }

    /**
     * @see \app\services\Route::ABOUT
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }
}
