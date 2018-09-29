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
use floor12\phone\PhoneFormatter;
use floor12\user\models\User;
use floor12\user\models\UserFilter;
use floor12\user\models\UserStatus;
use rmrevin\yii\fontawesome\FontAwesome;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = Yii::t('app.f12.user', 'Users');
?>

<?= Html::a(FontAwesome::icon('plus') . ' ' . Yii::t('app.f12.user', 'Add user'), null, [
    'onclick' => EditModalHelper::showForm(['/user/admin/form'], 0),
    'class' => 'btn btn-primary btn-sm pull-right'
]) ?>

    <h1><?= Yii::t('app.f12.user', 'Users') ?></h1>

<?php
Pjax::begin(['id' => 'items']);
echo GridView::widget([
    'layout' => '{items}{pager}{summary}',
    'dataProvider' => $model->dataProvider(),
    'tableOptions' => ['class' => 'table table-striped table-hover'],
    'columns' => [
        'id',
        'fullname',
        'email:email',
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
        ['contentOptions' => ['style' => 'min-width:100px;', 'class' => 'text-right'],
            'content' => function (User $model) {

                $html = Html::a(FontAwesome::icon('key'), NULL, [
                        'title' => Yii::t('app.f12.user', 'Send password reset link'),
                        'onclick' => "resetPassword($model->id,event)",
                        'class' => 'btn btn-default btn-sm'])
                    . ' ';

                $html .= Html::a(FontAwesome::icon('pencil'), NULL, [
                        'title' => Yii::t('app.f12.user', 'Edit user'),
                        'onclick' => EditModalHelper::showForm(['/user/admin/form'], $model->id),
                        'class' => 'btn btn-default btn-sm'])
                    . ' ';

                $html .= Html::a(FontAwesome::icon('trash'), NULL, [
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


