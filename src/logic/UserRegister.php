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
    /** @var User */
    protected $_model;
    /** @var array */
    protected $_data;
    /** @var bool */
    protected $sendWelcomeEmail;

    /**
     * UserRegister constructor.
     * @param User $model
     * @param array $data
     * @throws BadRequestHttpException
     */
    public function __construct(User $model, array $data, bool $sendWelcomeEmail = true)
    {
        if (!$model->isNewRecord)
            throw new BadRequestHttpException('This instance of User is already in databese.');

        $this->sendWelcomeEmail = $sendWelcomeEmail;
        $this->_model = $model;
        $this->_data = $data;

        $this->_model->created = $this->_model->updated = time();
        $this->_model->scenario = User::SCENARIO_REGISTER;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $this->_model->load($this->_data);
        $this->_model->generateAuthKey();

        if (empty($this->_model->password))
            $this->_model->password = substr(md5(time() . rand(999, 99999)), rand(0, 5), 8);

        $this->_model->setPassword($this->_model->password);


        if ($this->sendWelcomeEmail)
            $this->_model->on(User::EVENT_AFTER_INSERT, function ($event) {
                $this->sendWelcomeEmail();
            });


        return $this->_model->save();
    }

    /**
     * @return bool
     */
    protected function sendWelcomeEmail()
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => "@vendor/floor12/yii2-module-user/src/mail/user-registration-success-html.php"],
                ['user' => $this->_model]
            )
            ->setFrom([Yii::$app->params['no-replyEmail'] => Yii::$app->params['no-replyName']])
            ->setSubject(Yii::t('app.f12.user', 'You credentials'))
            ->setTo($this->_model->email)
            ->send();
    }

}
