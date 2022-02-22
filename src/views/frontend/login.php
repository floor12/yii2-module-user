<?php
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\form\LoginForm */

use floor12\user\assets\LoginAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

LoginAsset::register($this);

$this->title = Yii::t('app.f12.user', 'Login');

?>
<div class="row">
    <div class="col-sm-10 col-sm-offset-1 col-lg-4 col-lg-offset-4">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>


        <div id="login-password-block">
            <?= Html::a(Yii::t('app.f12.user', 'Forgot your password?'), ['/user/frontend/forget'], [
                'style' => 'float:right;'
            ]) ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>

        <?= Html::submitButton(Yii::t('app.f12.user', 'Go'), ['class' => 'btn btn-primary pull-right', 'name' => 'login-button']) ?>

        <?= Yii::$app->getModule('user')->allowRegister ?
            Html::a(Yii::t('app.f12.user', 'Signup'), ['/user/frontend/signup'], [
                'class' => 'btn btn-default'
            ]) : NULL ?>

        <br>
        <br>
        <?= $form->field($model, 'use_password')->checkbox(); ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
