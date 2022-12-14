<?php

namespace app\models\forms;

use app\models\Account;
use yii\base\Model;

class RegistrationForm extends Model
{
    public string $email = '';
    public string $password = '';
    public string $password_repeat = '';
    public bool $agree = false;

    public function rules(): array
    {
        return [
            [['email', 'password', 'password_repeat'], 'trim'],
            [['email', 'password', 'password_repeat'], 'required'],
            [['email', 'password', 'password_repeat'], 'string'],
            ['email', 'string', 'max' => 64],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => Account::class, 'targetAttribute' => 'email'],
            ['password', 'string', 'length' => [6, 256]],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            ['agree', 'boolean'],
            ['agree', 'compare', 'compareValue' => true, 'message' => 'Для регистрации необходимо принять соглашение'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email'           => 'Адрес электронной почты',
            'password'        => 'Пароль',
            'password_repeat' => 'Подтвердите пароль',
            'agree'           => false,
        ];
    }

    public function formName(): string
    {
        return 'Registration';
    }

    public function beforeValidate(): bool
    {
        $this->email = strtolower($this->email);
        return parent::beforeValidate();
    }
}