<?php

namespace floor12\user\controllers;

use floor12\editmodal\DeleteAction;
use floor12\editmodal\EditModalAction;
use floor12\user\logic\UserUpdate;
use floor12\user\models\ForgetPasswordForm;
use floor12\user\models\User;
use floor12\user\models\UserFilter;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 28.09.2018
 * Time: 13:11
 */
class AdminController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Yii::$app->getModule('user')->editRole],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['delete'],
                    'approve' => ['post'],
                ],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = Yii::$app->getModule('user')->adminLayout;
        parent::init();
    }

    public function actionIndex()
    {
        $model = new UserFilter();
        $model->load(Yii::$app->request->get());
        return $this->render(Yii::$app->getModule('user')->viewIndex, ['model' => $model]);
    }


    public function actionPasswordSend()
    {
        $model = User::findOne((int)Yii::$app->request->post('id'));

        if (!$model)
            throw new NotFoundHttpException('User is not found.');

        if (!Yii::createObject(ForgetPasswordForm::className(), [['email' => $model->email]])->sendEmail())
            throw new BadRequestHttpException("User is disabled.");

        return Yii::t('app.f12.user', 'Email is sent.');
    }


    public function actions()
    {
        return [
            'form' => [
                'class' => EditModalAction::class,
                'model' => User::class,
                'logic' => UserUpdate::class,
                'view' => Yii::$app->getModule('user')->viewForm,
                'message' => Yii::t('app.f12.user', 'The user is saved.')
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'model' => User::class,
                'message' => Yii::t('app.f12.user', 'The user is deleted.')
            ],
        ];
    }

}