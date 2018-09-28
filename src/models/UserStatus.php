<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 26.09.2018
 * Time: 19:39
 */

namespace floor12\user\models;

use yii2mod\enum\helpers\BaseEnum;

class UserStatus extends BaseEnum
{
    const STATUS_ACTIVE = 0;
    const STATUS_DISABLED = 1;

    public static $list = [
        self::STATUS_ACTIVE => 'Активен',
        self::STATUS_DISABLED => 'Выключен',
    ];

}