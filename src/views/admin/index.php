<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 29.09.2018
 * Time: 11:59
 *
 * @var $this View
 * @var $model UserFilter
 *
 */

use floor12\editmodal\EditModalHelper;
use floor12\editmodal\IconHelper;
use floor12\phone\PhoneFormatter;
use floor12\user\assets\UserAsset;
use floor12\user\models\User;
use floor12\user\models\UserFilter;
use floor12\user\models\UserStatus;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

UserAsset::register($this);

$this->registerJs("sendPasswordLinkConfirmText='" . Yii::t('app.f12.user', 'Do you want to send password reset link to this user?') . "'");

$this->title = Yii::t('app.f12.user', 'Users');


?>

<?= Html::a(IconHelper::PLUS . ' ' . Yii::t('app.f12.user', 'Add user'), null, [
    'onclick' => EditModalHelper::showForm(['/user/admin/form'], 0),
    'class' => 'btn btn-primary btn-sm pull-right'
]) ?>

    <h1><?= Yii::t('app.f12.user', 'Users') ?></h1>

<?php $form = ActiveForm::begin([
    'method' => 'GET',
    'options' => ['class' => 'autosubmit', 'data-container' => '#items'],
    'enableClientValidation' => false,
]); ?>
    <div class="filter-block">
        <div class="row">

            <?php if (Yii::$app->getModule('user')->useRbac): ?>
                <div class="col-md-7">
                    <?= $form->field($model, 'filter')->label(false)->textInput(['placeholder' => Yii::t('app.f12.user', 'Search in users'), 'autofocus' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'role')
                        ->label(false)
                        ->dropDownList(
                            array_map(function ($role) {
                                return $role->description;
                            }, Yii::$app->authManager->getRoles()),
                            ['prompt' => Yii::t('app.f12.user', 'All roles'),])
                    ?>
                </div>
            <?php else: ?>
                <div class="col-md-10">
                    <?= $form->field($model, 'filter')->label(false)->textInput(['placeholder' => Yii::t('app.f12.user', 'Search in users'), 'autofocus' => true]) ?>
                </div>
            <?php endif; ?>


            <div class="col-md-2">
                <?= $form->field($model, "status")->label(false)->dropDownList(UserStatus::listData(), ['prompt' => Yii::t('app.f12.user', 'Any status')]) ?>
            </div>

        </div>
    </div>

<?php
ActiveForm::end();
Pjax::begin(['id' => 'items']);
echo GridView::widget([
    'layout' => '{items}{pager}{summary}',
    'dataProvider' => $model->dataProvider(),
    'rowOptions' => function (User $model) {
        if ($model->status == UserStatus::STATUS_DISABLED) {
            return ['class' => 'user-disabled', 'style' => 'white-space: nowrap;'];
        }
        return ['style' => 'white-space: nowrap;'];
    },
    'tableOptions' => ['class' => 'table table-striped table-hover'],
    'columns' => [
        'id',
        'fullname',
        'email:email',
        [
            'content' => function (User $model) {
                if (Yii::$app->getModule('user')->useRbac) {
                    $roles = Yii::$app->authManager->getRolesByUser($model->id);

                    if (!$roles)
                        return;

                    $rolesHtml = array_map(function (\yii\rbac\Role $role) {
                        return Html::tag('span', $role->description, ['class' => 'role_tag']);
                    }, $roles);

                    return implode(' ', $rolesHtml);
                }
                return;
            }
        ],
        [
            'attribute' => 'phone',
            'content' => function (User $model) {
                return PhoneFormatter::run($model->phone);
            }
        ],
        [
            'attribute' => 'status',
            'content' => function (User $model) {
                return UserStatus::getLabel($model->status);
            }
        ],
        ['contentOptions' => ['style' => 'min-width:130px;', 'class' => 'text-right'],
            'content' => function (User $model) {

                $html = Html::a(\floor12\user\assets\IconHelper::SEND, NULL, [
                        'title' => Yii::t('app.f12.user', 'Send password reset link'),
                        'onclick' => "f12user.sendPasswordLink($model->id)",
                        'class' => 'btn btn-default btn-sm'])
                    . ' ';

                if (Yii::$app->getModule('user')->allowAdminToLoginAsAnyUser)
                    $html .= Html::a(\floor12\user\assets\IconHelper::LOGIN, NULL, [
                            'title' => Yii::t('app.f12.user', 'Login as user'),
                            'onclick' => EditModalHelper::showForm(['/user/admin/login'], ['user_id' => $model->id]),
                            'class' => 'btn btn-default btn-sm'])
                        . ' ';

                $html .= Html::a(IconHelper::PENCIL, NULL, [
                        'title' => Yii::t('app.f12.user', 'Edit user'),
                        'onclick' => EditModalHelper::showForm(['/user/admin/form'], $model->id),
                        'class' => 'btn btn-default btn-sm'])
                    . ' ';

                $html .= Html::a(IconHelper::TRASH, NULL, [
                        'title' => Yii::t('app.f12.user', 'Delete user'),
                        'onclick' => EditModalHelper::deleteItem(['/user/admin/delete'], $model->id),
                        'class' => 'btn btn-default btn-sm'
                    ]) . ' ';
                return $html;
            },
        ]
    ]
]);
Pjax::end();


