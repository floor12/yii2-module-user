<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 28.09.2018
 * Time: 20:55
 */

namespace floor12\user\logic;

use floor12\editmodal\LogicInterface;
use floor12\user\models\User;
use Yii;
use yii\db\ActiveRecordInterface;
use yii\web\IdentityInterface;

class UserUpdate implements LogicInterface
{
    private $_model;
    private $_data;
    private $_identity;

    public function __construct(ActiveRecordInterface $model, array $data, IdentityInterface $identity)
    {
        if (!$model instanceof User)
            throw new InvalidArgumentException('Model must be instance of class User');

        $this->_model = $model;
        $this->_data = $data;
        $this->_identity = $data;
        $this->_model->updated = time();
        $this->_model->scenario = User::SCENARIO_ADMIN;
    }


    public function execute()
    {
        $this->_model->load($this->_data);

        if ($this->_model->isNewRecord) {
            if (!$this->_model->password)
                $this->_model->password = Yii::$app->security->generateRandomString(8);

            $this->_model->created = time();
            $this->_model->generateAuthKey();
            $this->_model->on(User::EVENT_AFTER_INSERT, function ($event) {
                Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => "@vendor/floor12/yii2-module-user/src/mail/user-registration-success-html.php"],
                        ['user' => $this->_model]
                    )
                    ->setFrom([Yii::$app->params['no-replyEmail'] => Yii::$app->params['no-replyName']])
                    ->setSubject(Yii::t('app.f12.user', 'You credentials'))
                    ->setTo($this->_model->email)
                    ->send();
            });
        }

        $this->_model->on(User::EVENT_AFTER_UPDATE, function ($event) {
            $this->updateRoles();
        });

        $this->_model->on(User::EVENT_AFTER_INSERT, function ($event) {
            $this->updateRoles();
        });

        if ($this->_model->password)
            $this->_model->setPassword($this->_model->password);

        return $this->_model->save();
    }

    /**
     * @throws \Exception
     */
    protected function updateRoles()
    {
        if (!Yii::$app->getModule('user')->useRbac)
            return;

        Yii::$app->authManager->revokeAll($this->_model->id);

        if ($this->_model->permission_ids)
            foreach ($this->_model->permission_ids as $item) {
                $r = Yii::$app->authManager->getRole($item);
                Yii::$app->authManager->assign($r, $this->_model->id);
            }
    }

}