<?php

namespace floor12\user\models;

use Yii;
use yii\base\Model;
use yii\web\BadRequestHttpException;

/**
 * Password reset request form
 */
class ForgetPasswordForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => Yii::$app->getModule('user')->userModel,
                'filter' => ['status' => UserStatus::STATUS_ACTIVE],
                'message' => Yii::t('app.f12.user', 'There is no user with this email address.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
      
        $this->email = mb_convert_case($this->email, MB_CASE_LOWER);

        if (!$this->validate())
            return false;

        /* @var $user User */
        $user = Yii::$app->getModule('user')->userModel::findOne([
            'status' => UserStatus::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!Yii::$app->getModule('user')->userModel::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            $user->save(false, ['password_reset_token']);
        }

        $emailSend = Yii::$app
            ->mailer
            ->compose(
                ['html' => "@vendor/floor12/yii2-module-user/src/mail/user-password-reset-link.php"],
                [
                    'user' => $user,
                    'resetLink' => Yii::$app->urlManager->createAbsoluteUrl(['/user/frontend/reset-password', 'token' => $user->password_reset_token])
                ]
            )
            ->setFrom([Yii::$app->params['no-replyEmail'] => Yii::$app->params['no-replyName']])
            ->setSubject(Yii::t('app.f12.user', 'Password reset link'))
            ->setTo($user->email)
            ->send();

        if (!$emailSend)
            throw new BadRequestHttpException('Mail service error. Please contact administator.');

        return true;
    }
}
