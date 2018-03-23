<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */


/**
 *
 *  Interface for the backend to add a new test configuration to the database
 *
 */

session_start();
if($_POST && isset($_SESSION["userID"])) {

    require_once "../DatabaseHandler/TestDatabaseHandler.php";

    // create new TestDatabaseHandler instance
    $testDatabaseHandler = new TestDatabaseHandler();
    $testDatabaseHandler->create();

    if(isset($_POST["option"])) {
        if($_POST["option"] === "add") {
            // add a new test configuration in the database
            if(isset($_POST["antennaModel"]) && isset($_POST["projectID"])) {
                $testDatabaseHandler->add([ "project_id" => $_POST["projectID"], "antenna" => $_POST["antennaModel"]]);
                echo "success";
            }
        }
        else if($_POST["option"] === "setActive") {
            if(isset($_POST["activeTestID"])) {
                // activate specified test
                $testDatabaseHandler->setActive($_POST["activeTestID"]);
                echo "success";
            }
        }
    }
}
