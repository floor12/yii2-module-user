<?php

namespace floor12\user\assets;

use yii\web\AssetBundle;

class UserAsset extends AssetBundle
{
    public $sourcePath = '@vendor/floor12/yii2-module-user/src/assets/';
    public $css = [
        'css/f12.user.css'
    ]; 
    public $js = [
        'js/f12.user.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
