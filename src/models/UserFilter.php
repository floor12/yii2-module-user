<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 29.09.2018
 * Time: 11:59
 */

namespace floor12\user\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserFilter extends Model
{
    public $filter;
    public $status;

    public function rules()
    {
        return [
            ['filter', 'string'],
            ['status', 'integer'],
        ];
    }

    public function dataProvider()
    {
        return new ActiveDataProvider([
            'query' => User::find()
                ->andFilterWhere(['=', 'status', $this->status])
                ->andFilterWhere(['OR',
                    ['LIKE', 'fullname', $this->filter],
                    ['LIKE', 'email', $this->filter],
                    ['LIKE', 'phone', $this->filter],
                ])
        ]);
    }
}