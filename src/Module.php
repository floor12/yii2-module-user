<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 31.12.2017
 * Time: 14:45
 */

namespace floor12\user;

use floor12\user\logic\UserRegister;
use floor12\user\models\User;
use floor12\user\models\UserFilter;
use Yii;

class Module extends \yii\base\Module
{

    /** @var string */
    public $editRole = 'admin';

    /** @inheritdoc */
    public $controllerNamespace = 'floor12\user\controllers';

    /**  @var string */
    public $userAgreementUrl = '';

    /**  @var bool This option adds role selector to user model form */
    public $useRbac = false;

    /**  @var bool Allow register of new users */
    public $allowRegister = false;
    /**
     * @var string
     */
    public $afterLoginUrl;
    /**
     * @var string
     */
    public $userModel = User::class;
    /**
     * @var string
     */
    public $signUpLogic = UserRegister::class;
    /**
     * @var string
     */
    public $adminUserFilterClass = UserFilter::class;

    /**
     * Layouts and views
     */
    public $adminLayout = '@app/views/layouts/main';
    public $frontendLayout = '@app/views/layouts/main';
    public $viewIndex = '@vendor/floor12/yii2-module-user/src/views/admin/index';
    public $viewForm = '@vendor/floor12/yii2-module-user/src/views/admin/_form';
    public $viewSignup = '@vendor/floor12/yii2-module-user/src/views/frontend/signup';
    public $viewLogin = '@vendor/floor12/yii2-module-user/src/views/frontend/login';
    public $viewResetPassword = '@vendor/floor12/yii2-module-user/src/views/frontend/resetPassword';
    public $viewForgetPassword = '@vendor/floor12/yii2-module-user/src/views/frontend/forgetPassword';
    public $viewInfo = '@vendor/floor12/yii2-module-user/src/views/frontend/info';

    public $afterRegisterUrl = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->registerTranslations();
    }


    public function registerTranslations()
    {
        Yii::$app->i18n->translations['app.f12.user'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@vendor/floor12/yii2-module-user/src/messages',
            'sourceLanguage' => 'en-US',
            'fileMap' => [
                'app.f12.user' => 'user.php',
            ],
        ];
    }

}
