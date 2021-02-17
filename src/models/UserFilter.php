<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 29.09.2018
 * Time: 11:59
 */

namespace floor12\user\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;

class UserFilter extends Model
{
    public $filter;
    public $role;
    public $status;

    public function rules()
    {
        return [
            [['filter', 'role'], 'string'],
            ['status', 'integer'],
        ];
    }

    public function dataProvider()
    {
        if (!$this->validate())
            throw new BadRequestHttpException('Filter model validation error.');

        $classname = Yii::$app->getModule('user')->userModel;
        $query = $classname::find()
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['OR',
                ['LIKE', 'fullname', $this->filter],
                ['LIKE', 'email', $this->filter],
                ['LIKE', 'phone', $this->clearPhone($this->filter)],
            ]);

        if ($this->role) {
            $role_ids = Yii::$app->authManager->getUserIdsByRole($this->role);
            $query->andWhere(['IN', 'id', $role_ids]);

        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);
    }

    protected function clearPhone(string $value)
    {
        return str_replace([' ', '-', '(', ')', '_', '+'], '', trim($value));
    }
}
