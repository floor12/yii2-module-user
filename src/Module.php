<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 31.12.2017
 * Time: 14:45
 */

namespace floor12\user;

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
    public $useRbac = true;

    /**  @var bool Allow register of new users */
    public $allowRegister = false;

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