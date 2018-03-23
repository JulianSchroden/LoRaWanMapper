<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once "DatabaseHandler.php";

class ProjectDatabaseHandler extends DatabaseHandler {
    const tableName = "project";

    public function create() {
        $createQuery="CREATE TABLE IF NOT EXISTS `".self::tableName."`(
								`id`	       INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`description`  VARCHAR(255),
								`gateway_id`   VARCHAR(255),
								`gateway_desc` VARCHAR(255),
								`latitude`     FLOAT(10, 6),
								`longitude`    FLOAT(10, 6)	
						  )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->pdoInstance->query($createQuery);
    }

    /**
     * Adds a new project to the project database
     * @param $newElem   array containing the attributes:
     *                      - description   a description/ title for the project
     *                      - gateway_id    the ttn gateway id
     *                      - gateway_desc  the model of the gateway
     *                      - latitude      latitude of gateway's location
     *                      - longitude     longitude of gateway's location
     */
    public function add($newElem) {
        $statement = $this->pdoInstance->prepare(
            "INSERT INTO ".self::tableName." (`description`, `gateway_id`, `gateway_desc`, `latitude`, `longitude`) VALUES(:description, :gateway_id, :gateway_desc, :latitude, :longitude)");
        $statement->execute($newElem);
    }

    /**
     * Queries project database by provided project id and returns its data
     * @param $id
     * @return mixed
     */
    public function query($id) {
       $statement = $this->pdoInstance->prepare("SELECT * FROM ".self::tableName." WHERE `id`=:id");
       $statement->execute(["id" => $id]);
       return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Queries the database for the first entry and returns its data
     * @return mixed
     */
    public function queryFirst() {
        return $this->pdoInstance->query("SELECT * FROM ".self::tableName." LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all projects and returns the data as an array
     * @return array
     */
    public function queryAll() {
       return $this->pdoInstance->query("SELECT * FROM ".self::tableName)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        // TODO: Implement update() method.
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }
}