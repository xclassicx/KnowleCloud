<?php

namespace app\models;

use app\migrations\Tables;
use app\services\DbDate;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\web\IdentityInterface;

/**
 * Class Account
 **
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $auth_key
 *
 * @property string $first_name
 * @property string $second_name
 *
 * @property string $created
 * @property string $confirmed
 */
class Account extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return Tables::ACCOUNT;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['email', 'password', 'auth_key'], 'required'],
            [['email', 'password', 'auth_key'], 'string', 'max' => 255],
            [['first_name', 'second_name'], 'string', 'max' => 64],
            [['first_name', 'second_name'], 'match', 'pattern' => '/^[А-Я][а-яА-ЯёЁ\- ]+$/um', 'message' => 'Только кириллица, с большой буквы'],
            [['auth_key'], 'unique'],
            [['email'], 'unique', 'filter' => function (Query $query) {
                if ($this->isNewRecord) {
                    return;
                }
                $query->andWhere('id <> :cur_uid', ['cur_uid' => $this->getId()]);
            }],
            [['email'], 'email'],
            [['created', 'confirmed'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'email'       => 'Email',
            'password'    => 'Password',
            'auth_key'    => 'Auth Key',
            'created'     => 'Created',
            'confirmed'   => 'Confirmed',
            'first_name'  => 'First Name',
            'second_name' => 'Second Name',
        ];
    }

    public function beforeValidate(): bool
    {
        $this->email = strtolower($this->email);
        return parent::beforeValidate();
    }

    /** ********************* IdentityInterface start ********************* */
    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     *                       Null should be returned if such an identity cannot be found
     *                       or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id): ?Account
    {
        if (!intval($id)) {
            return null;
        }

        $user = self::findOne($id);
        if (!$user) {
            return null;
        }

        /**
         * Неподтвержденным нельзя авторизоваться
         *
         * @see  IdentityInterface::findIdentity() - секцию return
         */
        if (!$user->isConfirmed()) {
            return null;
        }

        return $user;
    }

    /**
     * Finds an identity by the given token.
     *
     * @param mixed $token the token to be looked for
     * @param mixed $type  the type of the token. The value of this parameter depends on the implementation.
     *                     For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface|null the identity object that matches the given token.
     *                     Null should be returned if such an identity cannot be found
     *                     or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null): ?Account
    {
        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     *
     * @return int an ID that uniquely identifies a user identity.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     *
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /** ********************* IdentityInterface end ********************* */

    public static function findByEmail(string $sEmail): ?static
    {
        $sEmail = strtolower($sEmail);
        return self::find()->whereEmail($sEmail)->one();
    }

    public function setAuthKey(string $sKey): static
    {
        $this->auth_key = $sKey;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function validatePassword($sPassword): bool
    {
        return password_verify($sPassword, $this->password);
    }

    public function getCreated(): ?\DateTime
    {
        return DbDate::fromDatabase($this->created);
    }

    public function isConfirmed(): bool
    {
        return (bool)$this->confirmed;
    }

    public function getConfirmed(): ?\DateTime
    {
        return DbDate::fromDatabase($this->confirmed);
    }

    public function setConfirmed(): static
    {
        $this->confirmed = new Expression('CURRENT_TIMESTAMP');;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function getSecondName(): string
    {
        return $this->second_name;
    }

    public function setSecondName(string $second_name): static
    {
        $this->second_name = $second_name;
        return $this;
    }

    /**
     * Возвращает осмысленное имя пользователя, в зависимости от того, что указано в профиле
     */
    public function getSiteName(): string
    {
        $name = trim(implode(' ', [$this->first_name, $this->second_name]));
        if (!$name) {
            $name = $this->getEmail();
        }

        return $name;
    }

    /**
     * @inheritdoc
     */
    public static function find(): AccountQuery
    {
        return new AccountQuery(get_called_class());
    }
}