<?php

namespace floor12\user\models;

use floor12\phone\PhoneValidator;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $fullname Полное имя
 * @property string $auth_key Ключ авторизации
 * @property string $password_hash Хеш пароля
 * @property string $password_reset_token Токен для сброса пароля
 * @property string $email Email
 * @property int $phone Телефон
 * @property int $status Статус
 * @property int $created Создан
 * @property int $updated Обновлен
 * @property int $mindbox_id ID Mindbox
 * @property array $permissions Роли и пермишены
 * @property array $permission_ids Роли и пермишены для работы в формах
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_ADMIN = 'admin';

    const PERMISSIONS_CACHE_TIME = 300;

    public $password;

    public $permission_ids = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fullname', 'email', 'phone', 'created', 'updated'], 'required'],
            [['created', 'updated'], 'integer'],
            [['fullname', 'email', 'password'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['password_reset_token'], 'unique'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => Yii::$app->getModule('user')->userModel, 'message' => Yii::t('app.f12.user', 'This email address has already been taken.')],
            ['status', 'default', 'value' => UserStatus::STATUS_ACTIVE, 'on' => self::SCENARIO_ADMIN],
            ['permission_ids', 'safe', 'on' => self::SCENARIO_ADMIN],
            ['status', 'in', 'range' => [UserStatus::STATUS_ACTIVE, UserStatus::STATUS_DISABLED], 'on' => self::SCENARIO_ADMIN],
            ['phone', PhoneValidator::class],
            ['phone', 'unique', 'targetClass' => Yii::$app->getModule('user')->userModel, 'message' => Yii::t('app.f12.user', 'This phone address has already been taken.')],
            ['password', 'required', 'on' => self::SCENARIO_REGISTER]
        ];
    }

    public function afterFind()
    {
        $this->permission_ids = $this->permissions;
        parent::afterFind();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fullname' => Yii::t('app.f12.user', 'Name'),
            'email' => Yii::t('app.f12.user', 'Email'),
            'phone' => Yii::t('app.f12.user', 'Phone'),
            'password' => Yii::t('app.f12.user', 'Password'),
            'status' => Yii::t('app.f12.user', 'Status'),
            'created' => Yii::t('app.f12.user', 'Created'),
            'updated' => Yii::t('app.f12.user', 'Updated'),
            'permission_ids' => Yii::t('app.f12.user', 'User roles'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => UserStatus::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }


    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function beforeSave($insert)
    {
        if ($this->email) {
            $this->email = mb_convert_case($this->email, MB_CASE_LOWER);
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        if (!Yii::$app->getModule('user')->useRbac)
            return [];

        if (Yii::$app->has('cache') && $permissions = Yii::$app->cache->get($this->getPermissionCacheKey()))
            return $permissions;

        $permissions = array_map(function ($permission) {
            return $permission->name;
        }, Yii::$app->authManager->getRolesByUser($this->id));
        if (Yii::$app->has('cache'))
            Yii::$app->cache->set($this->getPermissionCacheKey(), $permissions, self::PERMISSIONS_CACHE_TIME);
        return $permissions;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (Yii::$app->has('cache'))
            Yii::$app->cache->delete($this->getPermissionCacheKey());
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return string
     */
    protected function getPermissionCacheKey()
    {
        return "user-permission-{$this->id}";
    }


}
