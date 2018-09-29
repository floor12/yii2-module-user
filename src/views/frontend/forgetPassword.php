<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\form\PasswordResetRequestForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app.f12.user', 'Password reset');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-4 col-sm-offset-4">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Yii::t('app.f12.user', 'Please fill out your email. <br>A link to reset password will be sent there.') ?>
        </p>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app.f12.user', 'Send'), ['class' => 'btn btn-primary pull-right']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
