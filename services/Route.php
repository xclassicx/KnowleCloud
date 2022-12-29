<?php

namespace app\services;

use app\controllers\DocumentController;
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
    public const ROOT = '/' . SiteController::NAME . '/index';

    /** @see SiteController::actionAbout() */
    public const ABOUT = '/' . SiteController::NAME . '/about';

    /******************************** registration *******************************/
    /** @see RegistrationController::actionRegistration() */
    public const REGISTRATION = '/' . RegistrationController::NAME . '/registration';

    /** @see RegistrationController::actionEmailConfirm() */
    public const REGISTRATION_CONFIRM = '/' . RegistrationController::NAME . '/email-confirm';

    /******************************** login *******************************/
    /** @see LoginController::actionLogin() */
    public const LOGIN = '/' . LoginController::NAME . '/login';

    /** @see LoginController::actionLogout() */
    public const LOGOUT = '/' . LoginController::NAME . '/logout';

    /******************************** document *******************************/
    /** @see DocumentController::actionCreate() */
    public const DOCUMENT_CREATE = '/' . DocumentController::NAME . '/create';

    /**
     * <code>Params: &lt;iDocumentId:[1-9]\d*&gt;</code>
     *
     * @see DocumentController::actionView()
     */
    public const DOCUMENT_VIEW = '/' . DocumentController::NAME . '/view';

    /**
     * <code>Params: &lt;iDocumentId:[1-9]\d*&gt;</code>
     *
     * @see DocumentController::actionDownload()
     */
    public const DOCUMENT_DOWNLOAD = '/' . DocumentController::NAME . '/download';

    /**
     * <code>Params: &lt;iDocumentId:[1-9]\d*&gt;</code>
     *
     * @see DocumentController::actionUpdate()
     */
    public const DOCUMENT_UPDATE = '/' . DocumentController::NAME . '/update';

    /**
     * <code>Params: &lt;iDocumentId:[1-9]\d*&gt;</code>
     *
     * @see DocumentController::actionDelete()
     */
    public const DOCUMENT_DELETE = '/' . DocumentController::NAME . '/delete';

    /** @see DocumentController::actionMy() */
    public const DOCUMENT_MY = '/' . DocumentController::NAME . '/my';

    /**
     * Зарегистрированные URL, и их обработчики
     */
    public static function getRules(): array
    {
        return [
            /******************************** base *******************************/
            'GET /'                                    => self::ROOT,
            'GET /about.html'                          => self::ABOUT,

            /******************************** registration *******************************/
            'GET,POST /registration'                   => self::REGISTRATION,
            'GET /registration/confirm'                => self::REGISTRATION_CONFIRM,

            /******************************** login *******************************/
            'GET,POST /login'                          => self::LOGIN,
            'POST /logout'                             => self::LOGOUT,

            /******************************** document *******************************/
            'GET,POST /doc/create'                     => self::DOCUMENT_CREATE,
            'GET /doc/<iDocumentId:[1-9]\d*>'          => self::DOCUMENT_VIEW,
            'GET /doc/get/<iDocumentId:[1-9]\d*>'      => self::DOCUMENT_DOWNLOAD,
            'GET,POST /doc/upd/<iDocumentId:[1-9]\d*>' => self::DOCUMENT_UPDATE,
            'POST /doc/del/<iDocumentId:[1-9]\d*>'     => self::DOCUMENT_DELETE,
            'GET /doc/my'                              => self::DOCUMENT_MY,
        ];
    }
}