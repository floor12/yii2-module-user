<?php

use app\models\User;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $resetLink string */


?>

<p>
    <b><?= Yii::t('app.f12.user', 'Hello') ?>, <?= $user->fullname ?>!</b>
</p>
<p><?= Yii::t('app.f12.user', 'Follow the link below to reset your password:') ?></p>

<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

 