<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once "DatabaseHandler/TestDatabaseHandler.php";
require_once "DatabaseHandler/TestSessionDatabaseHandler.php";
require_once "DatabaseHandler/TestSessionDataDatabaseHandler.php";
require_once "config/LoRaWanMapperConfig.php";

/**
 *
 *  Interface for the TTN http integration
 *
 */

// check if $_GET parameter "key" is set and equals the stated key
if(isset($_GET["key"]) && $_GET["key"] === LoRaWanMapperConfig::TTN_HTTP_INTEGRATION_KEY) {

    // fetch the POST data and decode it
    $data        = file_get_contents('php://input');
    $decodedData = json_decode($data);

    // create needed database instances
    $testDatabaseHandler = new TestDatabaseHandler();
    $testDatabaseHandler->create();

    $testSessionDatabaseHandler = new TestSessionDatabaseHandler();
    $testSessionDatabaseHandler->create();

    $testSessionDataDatabaseHandler = new TestSessionDataDatabaseHandler();
    $testSessionDataDatabaseHandler->create();

    // as a packet can be received by multiple gateways, loop through the gateways array
    foreach ($decodedData->metadata->gateways as $gateway) {
        // query database for active test configurations with the same gateway_id
        $testID = null;
        $test   = $testDatabaseHandler->queryByGateway($gateway->gtw_id);
        if($test){
            $testID = $test["id"];
        }

        // if counter = 0, create a new TestSession
        $sessionID = null;
        if(intval($decodedData->counter) === 0) {
            $sessionID = $testSessionDatabaseHandler->add([
                "test_id"    => $testID,
                "gateway_id" => $gateway->gtw_id,
                "device_id"  => $decodedData->dev_id,
                "start_time" => $decodedData->metadata->time
            ]);
        }
        else {
            // get the id of the active session
            $sessionID = $testSessionDatabaseHandler->queryByDeviceAndGateway($gateway->gtw_id, $decodedData->dev_id)["id"];
        }

        // store the packet data in the session_data database
        $testSessionDataDatabaseHandler->add([
								"session_id" =>  $sessionID,
								"counter"    =>  $decodedData->counter,
                                "latitude"    => $decodedData->payload_fields->latitude,
                                "longitude"   => $decodedData->payload_fields->longitude,
                                "altitude"    => $decodedData->payload_fields->altitude,
                                "time"        => $decodedData->metadata->time,
                                "frequency"   => $decodedData->metadata->frequency,
                                "data_rate"   => $decodedData->metadata->data_rate,
                                "channel"     => $gateway->channel,
                                "rssi"        => $gateway->rssi,
                                "snr"         => $gateway->snr,
                                "json_data"   => $data
                    ]);

    }

}


/** Example Data

    {
        "app_id"          : "10000001",
        "dev_id"          : "00000001",
        "hardware_serial" : "0000000000000001",
        "port"            : 10,
        "counter"         : 2,
        "payload_raw"     : "aihIQmqO20AAgLJD",
        "payload_fields"  :
        {
            "altitude" : 357.0020446777344,
            "latitude" : 50.03931427001953,
            "longitude": 6.861114501953125
        },
        "metadata":
        {
            "time"        : "2017-10-31T15:09:17.250005379Z",
            "frequency"   : 868.3,
            "modulation"  : "LORA",
            "data_rate"   : "SF7BW125",
            "coding_rate" : "4/5",
            "gateways"    : [
                {
                    "gtw_id"    : "eui-9251636543371045",
                    "timestamp" : 81398667,
                    "time"      : "",
                    "channel"   : 1,
                    "rssi"      : -19,
                    "snr"       : 9.8,
                    "rf_chain"  : 1,
                    "latitude"  : 49.60836,
                    "longitude" : 7.168723,
                    "altitude"  : 355
                }]
        },
            "downlink_url" : "https://integrations.thethingsnetwork.org/ttn-eu/api/v2/down/10000001/test?key=ttn-account-v2.dnrLoUtKMc7y-FfkYCHccXVXjPucVXKeBDKeXR8e91w"
    }

*/
