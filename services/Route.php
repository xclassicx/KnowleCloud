<?php

namespace app\services;

use app\controllers\LoginController;
use app\controllers\SiteController;
use app\controllers\RegistrationController;

/**
 * Справочник по роутингу(URL и их обработчиков)
 */
class Route
{
    /******************************** base *******************************/
    /** @see SiteController::actionIndex() */
    const ROOT = '/' . SiteController::NAME . '/index';

    /** @see SiteController::actionAbout() */
    const ABOUT = '/' . SiteController::NAME . '/about';

    /******************************** registration *******************************/
    /** @see RegistrationController::actionRegistration() */
    const REGISTRATION = '/' . RegistrationController::NAME . '/registration';

    /** @see RegistrationController::actionEmailConfirm() */
    const REGISTRATION_CONFIRM = '/' . RegistrationController::NAME . '/email-confirm';

    /******************************** login *******************************/
    /** @see LoginController::actionLogin() */
    const LOGIN = '/' . LoginController::NAME . '/login';

    /** @see LoginController::actionLogout() */
    const LOGOUT = '/' . LoginController::NAME . '/logout';

    /**
     * Зарегистрированные URL, и их обработчики
     */
    public static function getRules(): array
    {
        return [
            /******************************** base *******************************/
            'GET /'                     => self::ROOT,
            'GET /about.html'           => self::ABOUT,

            /******************************** registration *******************************/
            'GET,POST /registration'    => self::REGISTRATION,
            'GET /registration/confirm' => self::REGISTRATION_CONFIRM,

            /******************************** login *******************************/
            'GET,POST /login'           => self::LOGIN,
            'POST /logout'              => self::LOGOUT,
        ];
    }
}