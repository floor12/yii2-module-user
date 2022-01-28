<?php

namespace floor12\user\assets;

use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $sourcePath = '@vendor/floor12/yii2-module-user/src/assets/';
    public $js = [
        'js/f12.login.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
