<?php
/**
 * DB接続用関数
 * PDOクラスのインスタンスを返す
 * @return PDO
 */
function getDb(): PDO {
    try {
        $dsn = 'mysql:dbname=your_database_name_here; host=127.0.0.1; charset=utf8';
        $user = 'your_username_here';
        $password = 'your_password_here';

        $db = new PDO($dsn, $user, $password);
        $db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);

        return $db;
    } catch (PDOException $e) {
        throw new Exception("DB接続エラー：{$e->getMessage()}");
    }
}
