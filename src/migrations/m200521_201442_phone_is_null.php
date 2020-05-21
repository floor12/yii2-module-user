<?php

use yii\db\Migration;

class m200521_201442_phone_is_null extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%user}}', 'email', $this->string()->null()->unique()->comment('Email'));
    }

    public function down()
    {
        $this->alterColumn('{{%user}}', 'email', $this->string()->notNull()->unique()->comment('Email'));
    }
}
