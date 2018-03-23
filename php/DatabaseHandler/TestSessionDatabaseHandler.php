<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once  "DatabaseHandler.php";

class TestSessionDatabaseHandler extends DatabaseHandler{
    const tableName = "session";

    public function create(){
        $createQuery="CREATE TABLE IF NOT EXISTS `".self::tableName."`(
								`id`	     INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`is_active`  BOOLEAN DEFAULT TRUE, 
								`test_id`    INTEGER UNSIGNED,
						        `gateway_id` VARCHAR(255),
						        `device_id`  VARCHAR(255),
						        `start_time` TIMESTAMP,
                                CONSTRAINT TestCascade FOREIGN KEY (`test_id`) REFERENCES test(`id`) ON DELETE CASCADE ON UPDATE CASCADE
						  )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->pdoInstance->query($createQuery);
    }

    /**
     * Adds a new session to the session database
     * @param $newElem   array containing the attributes:
     *                      - test_id      the id of the associated test
     *                      - gateway_id   the corresponding gateway_id
     *                      - device_id    the corresponding device_id
     *                      - start_time   the starting time of the session
     * @return string    The last insert id
     */
    public function add($newElem){
        // deactivate all old sessions with the same deviceID and gatewayID
        $statement=$this->pdoInstance->prepare("UPDATE ".self::tableName." SET `is_active` = FALSE WHERE `gateway_id` = :gateway_id AND `device_id` = :device_id");
        $statement->execute(["gateway_id" => $newElem["gateway_id"], "device_id" => $newElem["device_id"]]);

        $statement=$this->pdoInstance->prepare(
            "INSERT INTO ".self::tableName." (`test_id`,  `gateway_id`, `device_id`, `start_time`) VALUES(:test_id, :gateway_id, :device_id, :start_time)");
        $statement->execute($newElem);
        return $this->pdoInstance->lastInsertId();
    }

    public function query($id){
        // TODO: Implement query() method.
    }

    /**
     * Queries active session with provided gatewayID and deviceID and returns its data
     * @param $gatewayID   the corresponding gateway_id
     * @param $deviceID    the corresponding device_id
     * @return mixed
     */
    public function queryByDeviceAndGateway($gatewayID, $deviceID){
        $statement = $this->pdoInstance->prepare("SELECT * FROM ".self::tableName." WHERE `gateway_id` = :gateway_id AND `device_id` = :device_id AND `is_active` = TRUE");
        $statement->execute(["gateway_id" => $gatewayID, "device_id" => $deviceID]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function queryAll(){
        // TODO: Implement queryAll() method.
    }

    public function update($data){
        // TODO: Implement update() method.
    }

    public function delete($id){
        // TODO: Implement delete() method.
    }
}