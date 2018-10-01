<?php
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\form\LoginForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app.f12.user', 'Login');

?>
<div class="row">
    <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['id' => 'login-form',]); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= Html::submitButton(Yii::t('app.f12.user', 'Go'), ['class' => 'btn btn-primary pull-right', 'name' => 'login-button']) ?>

        <?= Html::a(Yii::t('app.f12.user', 'Forgot your password?'), ['/user/frontend/forget']) ?>
        <br>

        <?= Html::a(Yii::t('app.f12.user', 'Signup'), ['/user/frontend/signup']) ?>


        <?php ActiveForm::end(); ?>

    </div>
</div>