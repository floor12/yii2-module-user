<?php

use app\models\UserStatus;
use yii\db\Migration;

class m130524_201442_user extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'fullname' => $this->string()->notNull()->comment('Полное имя'),
            'auth_key' => $this->string(32)->notNull()->comment('Ключ авторизации'),
            'password_hash' => $this->string()->notNull()->comment('Хеш пароля'),
            'password_reset_token' => $this->string()->unique()->comment('Токен для сброса пароля'),
            'email' => $this->string()->notNull()->unique()->comment('Email'),
            'phone' => $this->string(14)->notNull()->unique()->comment('Телефон'),
            'status' => $this->smallInteger()->notNull()->defaultValue(UserStatus::STATUS_ACTIVE)->comment('Статус'),
            'created' => $this->integer()->notNull()->comment('Создан'),
            'updated' => $this->integer()->notNull()->comment('Обновлен'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
