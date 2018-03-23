<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once "DatabaseHandler/TestSessionDataDatabaseHandler.php";

/**
 *  Interface for the website's frontend to fetch all data related to a specific project
 */

if($_POST) {
    // create new TestSessionDataDatabaseHandler instance
    $testSessionDataDatabaseHandler = new TestSessionDataDatabaseHandler();
    if (isset($_POST["option"])) {
        if ($_POST["option"] === "fetch") {
            if (isset($_POST["projectID"])) {
                // responds with JSON encoded project data
                echo json_encode($testSessionDataDatabaseHandler->queryAllByProject($_POST["projectID"]));
            }
        }
    }
}