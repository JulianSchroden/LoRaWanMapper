<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once "DatabaseHandler.php";

/**
 * Class GeotaggedSignalDatabaseHandler
 * @deprecated interface for the old database scheme
 */


class GeotaggedSignalDatabaseHandler extends DatabaseHandler {
    const tableName="geotagged_signal";

    public function create() {
        $createQuery="CREATE TABLE IF NOT EXISTS `".self::tableName."`(
								`id`	      INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
								`gateway_id`  VARCHAR(255),
								`device_id`   VARCHAR(255),
								`latitude`    FLOAT(10, 6),
								`longitude`   FLOAT(10,6),
								`altitude`    FLOAT(7,3),
								`time`        TIMESTAMP,
								`frequency`   FLOAT,
								`modulation`  VARCHAR(255),
								`data_rate`   VARCHAR(255),
								`coding_rate` VARCHAR(255),
								`channel`     INTEGER UNSIGNED,
								`rssi`        INTEGER,
								`snr`         FLOAT,
								`json_data`   TEXT								
						  )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->pdoInstance->query($createQuery);
    }

    public function add($newElem) {
        $statement=$this->pdoInstance->prepare(
            "INSERT INTO ".self::tableName."
                            (`gateway_id`,`device_id`,`latitude`, `longitude`, `altitude`, `time`, `frequency`, `modulation`, `data_rate`, `coding_rate`, `channel`, `rssi`, `snr`, `json_data`) 
                      VALUES( :gateway_id, :device_id, :latitude,  :longitude,  :altitude,  :time,  :frequency,  :modulation,  :data_rate,  :coding_rate,  :channel,  :rssi,  :snr, :json_data)
                      "
        );
        $statement->execute($newElem);
    }

    public function query($id) {
        // TODO: Implement query() method.
    }

    public function queryAll() {
        $statement=$this->pdoInstance->prepare("SELECT `gateway_id`, `device_id`,`latitude`,`longitude`,`time`,`frequency`,`data_rate`,`coding_rate`,`channel`,`rssi`,`snr` FROM ".self::tableName." ");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryAllFiltered($params) {
        if(isset($params["dataRates"]) && isset($params["minRSSI"]) && isset($params["minSNR"])){
            // filter dataRates and implode result array to | separated string
            $allowedDataRates = ["SF7", "SF8", "SF9", "SF10", "SF11", "SF12"];
            $response=[];
            foreach ($params["dataRates"] as $dataRateInput) {
                if (in_array($dataRateInput, $allowedDataRates)) {
                    $statement=$this->pdoInstance->prepare("SELECT `latitude`,`longitude`,`time`,`data_rate`,`rssi`,`snr` 
                                                             FROM ".self::tableName."
                                                             WHERE `data_rate` LIKE CONCAT(:dataRate,'%') AND `rssi` >= :minRSSI AND `snr` >= :minSNR");
                    $statement->execute(["dataRate" => $dataRateInput, "minRSSI" => $params["minRSSI"], "minSNR" => $params["minSNR"]]);
                    $response[$dataRateInput]=$statement->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            return $response;
        }
        return null;
    }

    public function queryMinRSSI() {
        return $this->pdoInstance->query("SELECT MIN(`rssi`) FROM ".self::tableName)->fetchColumn();
    }

    public function queryMaxRSSI() {
        return $this->pdoInstance->query("SELECT MAX(`rssi`) FROM ".self::tableName)->fetchColumn();
    }

    public function queryMinSNR() {
        return $this->pdoInstance->query("SELECT MIN(`snr`) FROM ".self::tableName)->fetchColumn();
    }

    public function queryMaxSNR() {
        return $this->pdoInstance->query("SELECT MAX(`snr`) FROM ".self::tableName)->fetchColumn();
    }

    public function update($data) {
        // TODO: Implement update() method.
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }
}