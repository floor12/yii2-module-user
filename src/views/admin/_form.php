<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 24.10.2016
 * Time: 20:22
 *
 * @var \common\models\User $model
 * @var \yii\web\View $this
 *
 */

use floor12\user\models\UserStatus;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
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

    <div class="row">
        <div class="col-md-9">
            <?= $form->field($model, 'fullname') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropDownList(UserStatus::listData()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'email') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'phone')->widget(MaskedInput::class, (['mask' => ['+9 (999) 999-99-99', '+99 (999) 999-99-99']])) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>
    </div>

    <?php if (Yii::$app->getModule('user')->useRbac) {
        echo $form->field($model, 'permission_ids')->widget(Select2::class, [
            'data' => ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description'),
            'language' => 'ru',
            'pluginOptions' => [
                'multiple' => true,
                'allowClear' => true
            ],
        ]);
    } ?>


</div>


<div class="modal-footer">
    <?= Html::a('Отмена', '', ['class' => 'btn btn-default modaledit-disable']) ?>
    <?= Html::submitButton(Yii::t('app.f12.user', 'Save'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
