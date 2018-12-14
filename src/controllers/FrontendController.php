<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 28.09.2018
 * Time: 14:32
 */

namespace floor12\user\controllers;


use floor12\fprotector\Fprotector;
use floor12\user\logic\UserRegister;
use floor12\user\models\ForgetPasswordForm;
use floor12\user\models\LoginForm;
use floor12\user\models\ResetPasswordForm;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class FrontendController extends Controller
{
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
            return $this->goBack();
        }

        $model->password = '';

        return $this->render(Yii::$app->getModule('user')->viewLogin, [
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
        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return string
     */
    public function actionSignup()
    {
        if (!Yii::$app->getModule('user')->allowRegister)
            throw new ForbiddenHttpException(Yii::t('app.f12.user', 'Registrations is disabled.'));

        $model = Yii::createObject(Yii::$app->getModule('user')->userModel);

        if (Yii::$app->request->isPost) {
            Fprotector::check('User');
            if (Yii::createObject(Yii::$app->getModule('user')->signUpLogic, [$model, Yii::$app->request->post()])->execute()) {
                Yii::$app->user->login($model);
                return $this->render('info', [
                    'h1' => Yii::t('app.f12.user', 'Success!'),
                    'text' => Yii::t('app.f12.user', 'You have successfully registered and authorized.')
                ]);
            }
        }
        return $this->render(Yii::$app->getModule('user')->viewSignup, ['model' => $model,]);
    }

    /**
     * Requests password reset.
     *
     * @return string
     */
    public
    function actionForget()
    {
        $model = new ForgetPasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {
            return $this->render('info', [
                'h1' => Yii::t('app.f12.user', 'Email found.'),
                'text' => Yii::t('app.f12.user', 'Password reset link was send to your email. Please check your mailbox.')
            ]);
        }

        return $this->render(Yii::$app->getModule('user')->viewForgetPassword, [
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
    public
    function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            return $this->render('info', [
                'h1' => Yii::t('app.f12.user', 'Password changed.'),
                'text' => Yii::t('app.f12.user', 'You can signin with your email and new password.')
            ]);
        }

        return $this->render(Yii::$app->getModule('user')->viewResetPassword, [
            'model' => $model,
        ]);
    }

}