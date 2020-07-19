<?php
/**
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model User
 * @var $userAgreementUrl string
 */

use floor12\fprotector\Fprotector;
use floor12\user\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

$this->title = Yii::t('app.f12.user', 'Signup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">

        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fullname')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'phone')->widget(MaskedInput::class, (['mask' => ['+9 (999) 999-99-99', '+99 (999) 999-99-99']])) ?>

        <?= $form->field($model, 'email') ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="pull-left">
            <?= Html::a(Yii::t('app.f12.user', 'Password recovery'), ['/user/frontend/forget']) ?>
        </div>

        <div class="form-group text-right">
            <?= Fprotector::checkScript('User') ?>
            <?= Html::submitButton(Yii::t('app.f12.user', 'Save'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

        <?php if ($userAgreementUrl): ?>
            <p class="f12-user-info-block">
                <?= Yii::t('app.f12.user', 'By completing the registration procedure, you agree to our') ?>
                <?= Html::a(Yii::t('app.f12.user', 'personal data processing policy'), $userAgreementUrl) ?>.
            </p>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
