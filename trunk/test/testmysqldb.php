<?php
include_once '../mysqldb.php';

$databaseSchema = "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=3 ;

INSERT INTO `test` VALUES
(1, 'mates', '2012-11-19 16:08:10'),
(2, 'kuba', '2012-11-19 16:08:10');

DROP TABLE IF EXISTS `test_mesto`;
CREATE TABLE IF NOT EXISTS `test_mesto` (
  `test_id` int(11) NOT NULL,
  `mesto` varchar(255) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `test_mesto` VALUES
(1, 'Kladno'),
(2, 'Praha');
";

$databaseName = "test";
$host = "localhost";
$user = "root";
$password = "";

$config = new MysqlDbConfig();
$config->setUser("root");
$config->setPassword("");
$config->setDatabaseName("test");

$conn = new MysqlDb($config);


$conn->begin();

$res = $conn->execute("select * from test where id>%s", 0);
$row = $res->fetch_object();

//test explain mode
$conn->explainModeEnable(); 
$res = $conn->execute("select * from test LEFT JOIN test_mesto ON test.id = test_mesto.test_id where  test_mesto.mesto like 'Kladno'");
$res = $conn->execute("select * from test LEFT JOIN test_mesto ON test.id = test_mesto.test_id where  test_mesto.mesto like 'Kladno' order by test.name DESC");

$conn->commit();