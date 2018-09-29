<?php

namespace floor12\user\models;

use floor12\user\logic\UserRegister;
use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $fullname;
    public $email;
    public $phone;
    public $password;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['fullname', 'trim'],
            ['fullname', 'required'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'This phone address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fullname' => Yii::t('app.f12.user', 'Name'),
            'email' => Yii::t('app.f12.user', 'Email'),
            'phone' => Yii::t('app.f12.user', 'Phone'),
            'password' => Yii::t('app.f12.user', 'Password'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        $model = new User();
        if (Yii::createObject(UserRegister::class, [
            $model,
            [
                'fullname' => $this->fullname,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => $this->password,
            ]
        ]))
            return $model;
        return false;
    }
}
