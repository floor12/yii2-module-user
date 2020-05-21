<?php

use yii\db\Migration;

class m200521_201442_phone_is_null extends Migration
{
    public function up()
    {
        $this->alterColumn('{{%user}}', 'phone', $this->string(14)->null()->comment('Телефон'));
    }

    public function down()
    {
        $this->alterColumn('{{%user}}', 'phone', $this->string(14)->notNull()->unique()->comment('Телефон'));
    }
}
