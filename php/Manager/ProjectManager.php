<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */


/**
 *
 *  Interface for the backend to add a new project to the database
 *
 */


session_start();
if($_POST && isset($_SESSION["userID"])) {

    require_once "../DatabaseHandler/ProjectDatabaseHandler.php";
    // create new ProjectDatabaseHandler instance
    $projectDatabaseHandler = new ProjectDatabaseHandler();
    $projectDatabaseHandler->create();

    if(isset($_POST["option"])) {
        if($_POST["option"] === "add") {
            //add a new project to the database
            if(isset($_POST["projectDescription"]) && isset($_POST["gatewayModel"]) && isset($_POST["gatewayID"]) && isset($_POST["gatewayLocation"])) {
                $location = json_decode($_POST["gatewayLocation"]);
                $projectDatabaseHandler->add([  "description"  => $_POST["projectDescription"],
                                                "gateway_id"   => $_POST["gatewayID"],
                                                "gateway_desc" => $_POST["gatewayModel"],
                                                "latitude"     => $location->lat,
                                                "longitude"    => $location->lng]);
                echo "success";
            }
        }
    }
}
