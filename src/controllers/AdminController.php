<?php

namespace floor12\user\controllers;

use floor12\editmodal\DeleteAction;
use floor12\editmodal\EditModalAction;
use floor12\user\logic\UserUpdate;
use floor12\user\models\User;
use floor12\user\models\UserFilter;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

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

    public function actionIndex()
    {
        $model = new UserFilter();
        $model->load(Yii::$app->request->get());
        return $this->render('index', ['model' => $model]);
    }

    public function actions()
    {
        return [
            'form' => [
                'class' => EditModalAction::class,
                'model' => User::class,
                'logic' => UserUpdate::class,
                'view' => '_form',
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