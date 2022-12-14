<?php

namespace app\models\forms;

use yii\base\Model;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';
    public bool $rememberMe = false;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'trim'],
            [['email', 'password'], 'required'],
            [['email', 'password'], 'string'],
            [['email'], 'string', 'max' => 64],
            [['email'], 'email'],
            [['password'], 'string', 'length' => [6, 256]],
            [['rememberMe'], 'boolean'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function formName(): string
    {
        return 'Login';
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels(): array
    {
        return [
            'email'      => 'Адрес электронной почты',
            'password'   => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }
}
