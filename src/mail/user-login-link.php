<?php

use app\models\User;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $loginLink string */


?>

<p>
    <b><?= Yii::t('app.f12.user', 'Hello') ?>, <?= $user->fullname ?>!</b>
</p>
<p><?= Yii::t('app.f12.user', 'Follow the link below to login') ?>:</p>

<p><?= Html::a(Html::encode($loginLink), $loginLink) ?></p>

 