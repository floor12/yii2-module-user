<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 24.10.2016
 * Time: 20:22
 *
 * @var \common\models\User $model
 * @var \yii\web\View $this
 * @var $companies array
 * @var $offices array
 * @var $projects array
 * @var $workplaces array
 * @var $departments array
 *
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$form = ActiveForm::begin([
    'options' => ['class' => 'modaledit-form'],
    'enableClientValidation' => true
]); ?>

<div class="modal-header">
    <h2><?= Yii::t('app.f12.user', $model->isNewRecord ? "New user" : "User editing"); ?></h2>
</div>

<div class="modal-body">

    <?= $form->field($model, 'fullname') ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'phone')->widget(MaskedInput::class, (['mask' => ['+9 (999) 999-99-99', '+99 (999) 999-99-99']])) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>

</div>


<div class="modal-footer">
    <?= Html::a('Отмена', '', ['class' => 'btn btn-default modaledit-disable']) ?>
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
