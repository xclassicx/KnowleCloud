<?php

namespace app\controllers;

use app\models\Account;
use app\models\forms\RegistrationForm;
use app\services\Flash;
use app\services\Route;
use Exception;
use Throwable;
use Yii;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\validators\EmailValidator;
use yii\web\Controller;
use yii\web\Response;

class RegistrationController extends Controller
{
    const NAME = 'registration';
    const EMAIL_CONFIRM_SALT = 'Y+G6h]:ZXpA#Ldfj1aLeBE>4';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['registration'],
                'rules' => [
                    [
                        'roles' => ['?'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @see \app\services\Route::REGISTRATION
     */
    public function actionRegistration(): string|Response
    {
        $mForm = new RegistrationForm();
        if (!(Yii::$app->getRequest()->isPost && $mForm->load(Yii::$app->request->post()) && $mForm->validate())) {
            return $this->render('registration', ['mForm' => $mForm]);
        }

        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $mAccount = new Account();
            $mAccount->setEmail($mForm->email);
            $mAccount->setPassword($mForm->password);
            // Можно было бы заморочиться проверкой, что этот ключик свободен, но вероятность коллизии -> 0
            $mAccount->setAuthKey(Yii::$app->security->generateRandomString());

            if (!$mAccount->save()) {
                throw new Exception(implode(', ', $mAccount->getFirstErrors()));
            }

            // Confirm data
            $sConfirmData = Yii::$app->security->hashData($mAccount->getEmail(), self::EMAIL_CONFIRM_SALT);
            $urlApprove = Url::toRoute([Route::REGISTRATION_CONFIRM, 'sData' => $sConfirmData], true);
            // Send confirmation email
            $email = Yii::$app->mailer
                ->compose()
                ->setTextBody('Перейдите по ссылке для завершения регистрации: ' . $urlApprove)
                ->setTo($mAccount->getEmail())
                ->setSubject(Yii::$app->name . ' - регистрация');
            if (!$email->send()) {
                throw new UserException('Не смог отправить письмо с подтверждением регистрации');
            }
        } catch (Throwable $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());

            $message = 'Ошибка регистрации';
            if ($ex instanceof UserException) {
                $message = $ex->getMessage();
            }
            Flash::addDanger($message);

            return $this->render('registration', ['mForm' => $mForm]);
        }
        $transaction->commit();

        Flash::addSuccess("Вы успешно зарегистрировались! Осталось активировать аккаунт.");
        Flash::addSuccess("Проверьте электронную поту, ссылка есть в письме");

        return $this->goHome();
    }

    /**
     * Подтверждение email - переход сюда по ссылке из письма
     *
     * @see Route::REGISTRATION_CONFIRM
     */
    public function actionEmailConfirm(string $sData): Response
    {
        try {
            $sEmail = Yii::$app->security->validateData($sData, self::EMAIL_CONFIRM_SALT);
            if (!(new EmailValidator())->validate($sEmail)) {
                throw new UserException('Неверный email');
            }

            $mUser = Account::findByEmail($sEmail);
            if (!$mUser) {
                throw new UserException('Пользователь не найден. Email:' . $sEmail);
            }

            if ($mUser->isConfirmed()) {
                Flash::addSuccess('Вы уже активировали аккаунт');
                return $this->goHome();
            }

            $mUser->setConfirmed();
            if (!$mUser->save()) {
                throw new UserException('Не смог создать отметку о активации аккаунта');
            }
        } catch (UserException $ex) {
            Flash::addDanger($ex->getMessage());
            return $this->goHome();
        } catch (Throwable $ex) {
            Yii::error($ex);
            return $this->goHome();
        }

        Flash::addSuccess('Вы успешно активировали аккаунт');

        return $this->goHome();
    }
}