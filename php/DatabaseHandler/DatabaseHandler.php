<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

/**
 *
 *  Template class for all the database handlers to achieve consistent interfaces
 *
 */

require_once "DatabaseConnection.php";

abstract class DatabaseHandler {
    public $pdoInstance;
    function __construct() {
        $databaseConnection=new DatabaseConnection();
        $this->pdoInstance=$databaseConnection->open();
    }
    // creates the database table
    public abstract function create();

    // adds a single entry to the database
    public abstract function add($newElem);

    // queries an element by its id
    public abstract function query($id);

    // queries all elements in the database table
    public abstract function queryAll();

    // updates a single entry
    public abstract function update($data);

    // deletes a specified entry
    public abstract function delete($id);
}