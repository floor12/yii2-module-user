<?php

namespace floor12\user\controllers;


use floor12\user\models\User;
use floor12\user\Module;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 28.09.2018
 * Time: 13:11
 */
class ConsoleController extends Controller
{
    /**
     * @var Module
     */
    protected $userModule;
    /**
     * @var User
     */
    protected $user;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->userModule = Yii::$app->getModule('user');
    }

    public function actionAdd()
    {
        $this->user = new $this->userModule->userModel;
        $this->user->created = time();
        $this->user->updated = time();
        $this->readUserAttributeFromConsole('fullname');
        $this->readUserAttributeFromConsole('email');
        $this->readUserAttributeFromConsole('phone');
        $this->readUserAttributeFromConsole('password');

        if ($this->user->save())
            return $this->stdout("User saved with ID: {$this->user->id}" . PHP_EOL, Console::FG_GREEN);

        foreach ($this->user->getFirstErrors() as $error)
            $this->stdout($error . PHP_EOL, Console::FG_RED);
    }

    protected function readUserAttributeFromConsole(string $attribute)
    {
        do {
            if ($this->user->errors[$attribute])
                $this->stdout($this->user->errors[$attribute][0] . PHP_EOL, Console::FG_RED);
            $this->stdout("Enter user {$attribute}: " . PHP_EOL, Console::FG_GREEN);
            $this->user->{$attribute} = Console::stdin();
        } while (!$this->user->validate($attribute));

        if ($attribute == 'password')
            $this->user->setPassword($this->user->password);
    }

}
