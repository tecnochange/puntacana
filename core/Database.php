<?php

class Database {
		private static $connections = [];

		public static function connect($dbKey = 'main'){
			if(!isset(self::$connections[$dbKey])){
                $configs = require __DIR__ . '/var/www/html/puntacana.goforagile.com/config/databases.php';
                if(!array_key_exists($dbKey, $configs)){
                    die("Configuración de conexión '$dbKey' no encontrada.");
                }

                $config = $configs[$dbKey];

				try {
					$host = 'localhost';
					$user = 'root_goforagile';
					$pass = '93iuWi239JRza!Ld';
					$port = 3306;
					$charset = 'utf8';

					$dns = "mysql:host=$host;port=$port;dbname=$config;charset=$charset";
					$pdo = new PDO($dns,$user,$pass);
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					self::$connections[$dbKey] = $pdo;
				} catch (PDOException $th) {
					die("No fue posible establecer conexión.".$th->getMessage());
				}
			}
			return self::$connections[$dbKey];
		}
	}
?>