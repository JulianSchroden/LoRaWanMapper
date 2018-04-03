<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

    require_once __DIR__ . "/../config/LoRaWanMapperConfig.php";
	class DatabaseConnection {

        const host     = LoRaWanMapperConfig::DATABASE_LOGIN["host"];
        const user     = LoRaWanMapperConfig::DATABASE_LOGIN["user"];
        const password = LoRaWanMapperConfig::DATABASE_LOGIN["password"];
        const dbName   = LoRaWanMapperConfig::DATABASE_LOGIN["dbName"];


		public $pdoInstance;
		
		public function open() {
			$this->pdoInstance=new PDO('mysql:host='.self::host.';dbname='.self::dbName, self::user, self::password,  array(
			    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
			    PDO::ATTR_EMULATE_PREPARES => false)
            );
			return $this->pdoInstance;
		}
		
		public function close() {
			$this->pdoInstance=null;
		}
		
	}