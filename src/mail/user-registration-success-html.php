<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 08.08.2018
 * Time: 22:21
 *
 * @var $this View
 * @var $user User
 */

use floor12\user\models\User;
use yii\web\View;

?>
<p>
    <b><?= Yii::t('app.f12.user', 'Hello') ?>, <?= $user->fullname ?>!</b>
</p>

<p>
    <?= Yii::t('app.f12.user', 'You have successfully registered and can be authorized with the following credentials') ?>
    :
</p>

<p>
    <?= Yii::t('app.f12.user', 'Email') ?>: <?= $user->email ?>
    <br>
    <?= Yii::t('app.f12.user', 'Password') ?>: <?= $user->password ?>
</p>