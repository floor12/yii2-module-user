<?php

namespace floor12\user\models;

use Yii;
use yii\base\Model;
use yii\web\BadRequestHttpException;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $this->currentUser This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $use_password = false;

    /** @var null|User */
    private $currentUser = null;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            [['password'], 'required', 'when' => function (self $model) {
                return $model->use_password;
            }],
            [['email'], 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => Yii::$app->getModule('user')->userModel, 'targetAttribute' => 'email',
                'message' => Yii::t('app.f12.user', 'User with this email is not found.')
            ],
            ['use_password', 'boolean'],
            ['password', 'validatePassword', 'when' => function (self $model) {
                return $model->use_password;
            }],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $this->currentUser = $this->getCurrentUser();

            if (!$this->currentUser || !$this->currentUser->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app.f12.user', 'Incorrect email or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        $this->email = mb_convert_case($this->email, MB_CASE_LOWER);

        if ($this->validate() == false) {
            return false;
        }

        if ($this->use_password) {
            return Yii::$app->user->login($this->getCurrentUser(), 3600 * 24 * 30);
        } else {
            return $this->sendLoginEmail();
        }

        return false;
    }

    public function sendLoginEmail()
    {
        if (!Yii::$app->getModule('user')->userModel::isPasswordResetTokenValid($this->getCurrentUser()->password_reset_token)) {
            $this->getCurrentUser()->generatePasswordResetToken();
            $this->getCurrentUser()->save(false, ['password_reset_token']);
        }

        $emailSend = Yii::$app
            ->mailer
            ->compose(
                ['html' => "@vendor/floor12/yii2-module-user/src/mail/user-login-link.php"],
                [
                    'user' => $this->getCurrentUser(),
                    'loginLink' => Yii::$app->urlManager->createAbsoluteUrl(['/user/frontend/login-link', 'token' => $this->currentUser->password_reset_token])
                ]
            )
            ->setFrom([Yii::$app->params['no-replyEmail'] => Yii::$app->params['no-replyName']])
            ->setSubject(Yii::t('app.f12.user', 'Your login link'))
            ->setTo($this->getCurrentUser()->email)
            ->send();

        if (!$emailSend)
            throw new BadRequestHttpException('Mail service error. Please contact administator.');

        return true;
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app.f12.user', 'Email'),
            'password' => Yii::t('app.f12.user', 'Password'),
            'use_password' => Yii::t('app.f12.user', 'Use password'),
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getCurrentUser()
    {
        if ($this->currentUser === null) {
            $this->currentUser = UserQuery::findByEmail($this->email);
        }
        return $this->currentUser;
    }
}
