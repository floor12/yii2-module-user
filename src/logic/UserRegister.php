<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 28.09.2018
 * Time: 20:55
 */

namespace floor12\user\logic;

use floor12\user\models\User;
use Yii;
use yii\web\BadRequestHttpException;

class UserRegister
{
    protected $_model;
    protected $_data;

    public function __construct(User $model, array $data)
    {
        if (!$model->isNewRecord)
            throw new BadRequestHttpException('This instance of User is already in databese.');

        $this->_model = $model;
        $this->_data = $data;

        $this->_model->created = $this->_model->updated = time();
        $this->_model->scenario = User::SCENARIO_REGISTER;
    }


    public function execute()
    {
        $this->_model->load($this->_data);
        $this->_model->generateAuthKey();
        $this->_model->setPassword($this->_model->password);


        $this->_model->on(User::EVENT_AFTER_INSERT, function ($event) {
            Yii::$app
                ->mailer
                ->compose(
                    ['html' => "@vendor/floor12/yii2-module-user/src/mail/user-registration-success-html.php"],
                    ['user' => $this->_model]
                )
                ->setFrom([Yii::$app->params['no-replayEmail'] => Yii::$app->params['no-replayName']])
                ->setSubject(Yii::t('app.f12.user', 'You credentials'))
                ->setTo($this->_model->email)
                ->send();
        });


        return $this->_model->save();
    }

}