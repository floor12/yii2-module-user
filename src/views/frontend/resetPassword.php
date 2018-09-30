<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\form\ResetPasswordForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app.f12.user', 'Reset password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-4 col-sm-offset-4">

        <h1><?= Html::encode($this->title) ?></h1>

        <p><?= Yii::t('app.f12.user', 'Please choose your new password:') ?></p>

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <?= $form->field($model, 'password')->label(false)->passwordInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app.f12.user', 'Save'), ['class' => 'btn btn-primary pull-right']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
