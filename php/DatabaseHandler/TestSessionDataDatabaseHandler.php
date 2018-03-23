<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once "DatabaseHandler.php";
class TestSessionDataDatabaseHandler extends DatabaseHandler{
    const tableName = "session_data";


    public function create(){
        $createQuery="CREATE TABLE IF NOT EXISTS `".self::tableName."`(
								`id`	      INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`session_id`  INTEGER UNSIGNED,
								`counter`     INTEGER UNSIGNED NOT NULL,
						        `latitude`    FLOAT(10, 6),
								`longitude`   FLOAT(10,6),
								`altitude`    FLOAT(7,3),
								`time`        TIMESTAMP,
								`frequency`   FLOAT,
								`data_rate`   VARCHAR(255),
								`channel`     INTEGER UNSIGNED,
								`rssi`        INTEGER,
								`snr`         FLOAT,
								`json_data`   TEXT,
                                CONSTRAINT SessionCascade FOREIGN KEY (`session_id`) REFERENCES session(`id`) ON DELETE CASCADE ON UPDATE CASCADE
						  )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->pdoInstance->query($createQuery);
        return $this->pdoInstance->lastInsertId();
    }

    /**
     * @param $newElem   array containing the attributes:
     *                      - session_id   the id of the associated session
     *                      - counter      the counter of the package
     *                      - latitude     transmission location latitude
     *                      - longitude    transmission location longitude
     *                      - altitude     transmission location altitude
     *                      - time         transmission time
     *                      - frequency    transmission frequency
     *                      - data_rate    transmission data_rate
     *                      - channel      transmission channel
     *                      - rssi         signal strength of the package
     *                      - snr          signal to noise ratio of the package
     *                      - json_data    the entire package data JSON encoded
     */
    public function add($newElem){
        $statement=$this->pdoInstance->prepare(
            "INSERT INTO ".self::tableName." (`session_id`,  `counter`, `latitude`, `longitude`, `altitude`, `time`, `frequency`, `data_rate`, `channel`, `rssi`, `snr`, `json_data` ) 
                                                VALUES( :session_id,   :counter,  :latitude,  :longitude,  :altitude,  :time,  :frequency,  :data_rate,  :channel,  :rssi,  :snr, :json_data  )");
        $statement->execute($newElem);
    }

    public function query($id){
        // TODO: Implement query() method.
    }

    public function queryAll(){
        // TODO: Implement queryAll() method.
    }

    /**
     * Queries all session data related to a project
     * @param $projectID   the project id
     * @return array       array structured like this:
     *                      [
     *                          "testID" => [
     *                              "dataRate" => [
     *
     *                                               array containing corresponding session data
     *
     *                                            ]
     *                                      ]
     *                      ]
     */
    public function queryAllByProject($projectID){
        // get testIDs by projectID
        $statement = $this->pdoInstance->prepare("SELECT `id` FROM `test` WHERE `project_id` = :project_id");
        $statement->execute(["project_id" => $projectID]);
        $testIDs = $statement->fetchALL(PDO::FETCH_COLUMN);

        $dataRates = ["SF7", "SF8", "SF9", "SF10", "SF11", "SF12"];
        $response  = [];

        // loop through all test ids
        foreach ($testIDs as $testID){
            // loop through all dataRates
            foreach ($dataRates as $dataRate) {
                // query session data by testID and dataRate
                $statement = $this->pdoInstance->prepare(
                    "SELECT `id`, `counter`, `latitude`, `longitude`, `data_rate`, `rssi`, `snr` FROM `session_data` 
                              WHERE `session_data`.`session_id` IN (SELECT `session`.`id` FROM `session` WHERE `session`.`test_id` = :testID) 
                              AND `session_data`.`data_rate` LIKE CONCAT(:dataRate,'%')");
                $statement->execute(["testID" => $testID,"dataRate" => $dataRate]);

                // add array to response array
                $response[$testID][$dataRate] = $statement->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $response;
    }

    /**
     * Fetches the min and max RSSI values of a project
     * @param $projectID
     * @return mixed
     */
    public function queryRSSI_Bounds($projectID){
        $statement = $this->pdoInstance->prepare("
            SELECT MIN(`session_data`.`rssi`) AS min, MAX(`session_data`.`rssi`) as max
            FROM `session_data` 
            WHERE `session_data`.`session_id` IN(SELECT `session`.`id` 
                                                 FROM `session`
                                                 WHERE `session`.`test_id` IN(SELECT `test`.`id` 
                                                                              FROM `test` 
                                                                              WHERE `test`.`project_id` = :projectID))
        ");
        $statement->execute(["projectID" => $projectID]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches the min and max SNR values of a project
     * @param $projectID
     * @return mixed
     */
    public function querySNR_Bounds($projectID){
        $statement = $this->pdoInstance->prepare("
            SELECT MIN(`session_data`.`snr`) AS min, MAX(`session_data`.`snr`) as max
            FROM `session_data` 
            WHERE `session_data`.`session_id` IN(SELECT `session`.`id` 
                                                 FROM `session`
                                                 WHERE `session`.`test_id` IN(SELECT `test`.`id` 
                                                                              FROM `test` 
                                                                              WHERE `test`.`project_id` = :projectID))
        ");
        $statement->execute(["projectID" => $projectID]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data){
        // TODO: Implement update() method.
    }

    public function delete($id){
        // TODO: Implement delete() method.
    }
}