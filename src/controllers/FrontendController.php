<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 28.09.2018
 * Time: 14:32
 */

namespace floor12\user\controllers;


use floor12\fprotector\Fprotector;
use floor12\user\models\ForgetPasswordForm;
use floor12\user\models\LoginForm;
use floor12\user\models\ResetPasswordForm;
use floor12\user\models\TokenLoginForm;
use floor12\user\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class FrontendController extends Controller
{
    /**
     * @var Module
     */
    protected $userModule;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->userModule = Yii::$app->getModule('user');
        $this->layout = $this->userModule->frontendLayout;
        parent::init();
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if ($model->use_password) {
                $afterLoginUrl = Yii::$app->user->getReturnUrl($this->userModule->afterLoginUrl);
                return Yii::$app->getResponse()->redirect($afterLoginUrl);
            } else {
                return $this->render($this->userModule->viewInfo, [
                    'h1' => Yii::t('app.f12.user', 'Email with link was sent.'),
                    'text' => Yii::t('app.f12.user', 'Just click the link in the email and you will logged in.'),
                ]);
            }
        }

        $model->password = '';

        return $this->render($this->userModule->viewLogin, [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return Yii::$app->getResponse()->redirect(Yii::$app->request->referrer ?? '/');
    }

    /**
     * Signs user up.
     *
     * @return string
     */
    public function actionSignup()
    {
        if (!$this->userModule->allowRegister)
            throw new ForbiddenHttpException(Yii::t('app.f12.user', 'Registrations is disabled.'));

        $model = Yii::createObject($this->userModule->userModel);
        $model->setScenario($this->userModule->userModel::SCENARIO_REGISTER);

        if (Yii::$app->request->isPost) {

            Fprotector::check('User');

            if (Yii::createObject($this->userModule->signUpLogic, [$model, Yii::$app->request->post()])->execute()) {

                Yii::$app->user->login($model);

                if ($this->userModule->afterRegisterUrl)
                    return $this->redirect($this->userModule->afterRegisterUrl);

                return $this->render($this->userModule->viewInfo, [
                    'h1' => Yii::t('app.f12.user', 'Success!'),
                    'text' => Yii::t('app.f12.user', 'You have successfully registered and authorized.')
                ]);
            }
        }
        return $this->render($this->userModule->viewSignup, [
            'model' => $model,
            'userAgreementUrl' => $this->module->userAgreementUrl
        ]);
    }

    public function actionLoginLink($token)
    {
        try {
            $model = new TokenLoginForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        $model->login();
        $afterLoginUrl = Yii::$app->user->getReturnUrl($this->userModule->afterLoginUrl);
        return Yii::$app->getResponse()->redirect($afterLoginUrl);
    }

    /**
     * Requests password reset.
     * @param string|null $email
     * @return string
     */
    public function actionForget($email = null)
    {
        $model = new ForgetPasswordForm();
        $model->email = $email;
        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {
            return $this->render($this->userModule->viewInfo, [
                'h1' => Yii::t('app.f12.user', 'Email found.'),
                'text' => Yii::t('app.f12.user', 'Password reset link was send to your email. Please check your mailbox.')
            ]);
        }

        return $this->render($this->userModule->viewForgetPassword, [
            'model' => $model,
        ]);
    }


    /**
     * Resets password.
     *
     * @param string $token
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            return $this->render($this->userModule->viewInfo, [
                'h1' => Yii::t('app.f12.user', 'Password changed.'),
                'text' => Yii::t('app.f12.user', 'You can signin with your email and new password.')
            ]);
        }

        return $this->render($this->userModule->viewResetPassword, [
            'model' => $model,
        ]);
    }

}
