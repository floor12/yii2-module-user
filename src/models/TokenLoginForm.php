<?php

namespace floor12\user\models;

use app\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;

/**
 * Password reset form
 */
class TokenLoginForm extends Model
{
    public $password;

    /**
     * @var User
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException(Yii::t('app.f12.user', 'Login token cannot be blank.'));
        }
        $this->_user = UserQuery::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidParamException(Yii::t('app.f12.user', 'Wrong login token.'));
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app.f12.user', 'Password')
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function login()
    {
        $this->_user;
        $this->_user->generatePasswordResetToken(); // changing token after login
        $this->_user->save(false, ['password_reset_token']);
        return Yii::$app->user->login($this->_user, 3600 * 24 * 30);
    }
}
