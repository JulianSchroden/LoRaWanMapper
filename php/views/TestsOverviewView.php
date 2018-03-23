<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */


require_once  __DIR__."/../DatabaseHandler/TestDatabaseHandler.php";
require_once  __DIR__."/../DatabaseHandler/ProjectDatabaseHandler.php";

class TestsOverviewView {

    /**
     * Creates tests overview layout and returns it as a string
     * @param $projectID   Integer value which contains a project id
     * @return string
     */
    public static function create($projectID) {
        // create new ProjectDatabaseHandler instance
        $projectDatabaseHandler = new ProjectDatabaseHandler();
        $projectDatabaseHandler->create();

        // query project data by the provided projectID
        $projectData = $projectDatabaseHandler->query($projectID);

        // create new TestDatabaseHandler instance
        $testDatabaseHandler = new TestDatabaseHandler();
        $testDatabaseHandler->create();

        // query all tests related to the project
        $tests = $testDatabaseHandler->queryAllByProjects($projectID);

        // create test list entry layouts
        $testLayouts = "";
        for($i = 0; $i < count($tests); $i++) {
            $checked = "";
            if($tests[$i]["is_active"]) {
                $checked = "checked";
            }
            $testLayouts .= "
                        <div class='test-overview' >
                           
                            <h3>Antenna: {$tests[$i]["antenna"]}</h3>
                           
                            <div class='active-switch-wrapper'>
                                <input name='configuration' type='radio' data-id='{$tests[$i]["id"]}' {$checked}>
                            </div>
                        </div>";
        }

        return "

        <!doctype html>
        <html lang='de'>
            <head>
                <meta name='viewport' content='width=device-width, initial-scale=1'>
                <meta charset='utf-8'>
                <title>Tests</title>
                <meta name='theme-color' content='#000000'>
                <link rel='stylesheet' href='/css/style.css'>
                <link rel='stylesheet' href='/css/backend_style.css'>
                <link rel='stylesheet' href='/css/tests-overview-style.css'>
                <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
                <script src='/js/utils.js'></script>
                <script src='/js/backend.js'></script>
                
              
            </head>
        
        
        
            <body>
                <div class='title'>
                     <span class='backArrow'>
                        <img src='/img/arrow-left.png' alt='back' width='36' height='36' style='margin:14px;'>
                    </span>
                    <h1>Project: {$projectData["description"]}</h1>
                </div>
             
                   
                <div class='content' id='testsOverview'>
                    <h2>Antenna configurations:</h2>
                   
                    
                    {$testLayouts}
                    
                    
                   <div class='fab' id='newTestFAB'>
                        <span>+</span>
                    </div>
                </div>
                
            </body>
        </html>

    ";
    }
}