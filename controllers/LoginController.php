<?php

namespace app\controllers;

use app\models\Account;
use app\models\forms\LoginForm;
use app\services\Flash;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class LoginController extends Controller
{
    const NAME = 'login';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @see \app\services\Route::LOGIN
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $mForm = new LoginForm();
        if (!(Yii::$app->request->isPost && $mForm->load(Yii::$app->request->post()) && $mForm->validate())) {
            $mForm->password = '';
            return $this->render('login', [
                'mForm' => $mForm,
            ]);
        }

        $user = Account::findByEmail($mForm->email);
        if (!$user || !$user->isConfirmed() || !$user->validatePassword($mForm->password)) {
            Flash::addDanger('Неверный(или неподтвержденный) адрес электронной почты или пароль');
            $mForm->password = '';
            return $this->render('login', [
                'mForm' => $mForm,
            ]);
        }

        Yii::$app->user->login($user, $mForm->rememberMe ? 3600 * 24 * 30 : 0);
        return $this->goBack();
    }

    /**
     * @see \app\services\Route::LOGOUT
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}