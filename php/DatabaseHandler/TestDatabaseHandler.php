<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once "DatabaseHandler.php";

class TestDatabaseHandler extends DatabaseHandler{
    const tableName = "test";

    public function create(){
        $createQuery="CREATE TABLE IF NOT EXISTS `".self::tableName."`(
								`id`	            INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
								`project_id`        INTEGER UNSIGNED NOT NULL,
								`is_active`         BOOLEAN DEFAULT TRUE,
								`antenna`           VARCHAR(255),
								PRIMARY KEY(`id`, `project_id`),
                                CONSTRAINT ProjectCascade FOREIGN KEY (`project_id`) REFERENCES project(`id`) ON DELETE CASCADE ON UPDATE CASCADE
						  )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->pdoInstance->query($createQuery);
    }


    /**
     * Adds a new test to the test database
     * @param $newElem   array containing the attributes:
     *                      - project_id   the id of the associated project
     *                      - antenna      description of the antenna configuration
     */
    public function add($newElem){
        // deactivate all old tests with the same projectID
        $statement=$this->pdoInstance->prepare("UPDATE ".self::tableName." SET `is_active` = FALSE WHERE `project_id` = :project_id");
        $statement->execute(["project_id" => $newElem["project_id"]]);

        // add new entry to test table
        $statement=$this->pdoInstance->prepare(
            "INSERT INTO ".self::tableName." (`project_id`,  `antenna`) VALUES(:project_id, :antenna)");
        $statement->execute($newElem);
    }

    /**
     * Queries test database by provided test id and returns its data
     * @param $id
     * @return mixed
     */
    public function query($id){
        $statement =$this->pdoInstance->prepare("SELECT * FROM ".self::tableName." WHERE `id`=:id");
        $statement->execute(["id"=>$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all tests and returns the data as an array
     * @return array
     */
    public function queryAll(){
        return $this->pdoInstance->query("SELECT * FROM ".self::tableName)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all tests associated to a project and returns the data as an array
     * @param $projectID   the id of the associated project
     * @return array
     */
    public function queryAllByProjects($projectID){
        $statement = $this->pdoInstance->prepare("SELECT * FROM ".self::tableName." WHERE `project_id` = :project_id");
        $statement->execute(["project_id"=>$projectID]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches active test associated to a gateway and returns the data
     * @param $gatewayID   the id of the associated gateway
     * @return array
     */
    public function queryByGateway($gatewayID){
        $statement = $this->pdoInstance->prepare("SELECT test.id, test.project_id, test.is_active, test.antenna FROM ".self::tableName." INNER JOIN project ON(test.project_id = project.id) WHERE project.gateway_id = :gateway_id AND test.is_active = TRUE");
        $statement->execute(["gateway_id" => $gatewayID]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Activates a test configuration and disables all other tests
     * @param $id
     */
    public function setActive($id){
        // deactivate all other tests
        $statement=$this->pdoInstance->prepare("UPDATE ".self::tableName." SET `is_active` = FALSE WHERE `id` != :id");
        $statement->execute(["id" => $id]);

        $statement=$this->pdoInstance->prepare(
            "UPDATE ".self::tableName." SET `is_active` = TRUE WHERE `id` = :id");
        $statement->execute(["id" => $id]);
    }

    public function update($data){
        // TODO: Implement update() method.
    }

    public function delete($id){
        // TODO: Implement delete() method.
    }
}