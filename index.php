<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once "php/DatabaseHandler/ProjectDatabaseHandler.php";
require_once "php/DatabaseHandler/TestDatabaseHandler.php";
require_once "php/DatabaseHandler/TestSessionDataDatabaseHandler.php";
require_once "php/config/LoRaWanMapperConfig.php";

// create new ProjectDatabaseHandler instance
$projectDatabaseHandler = new ProjectDatabaseHandler();
$projectDatabaseHandler->create();

// query project data by the provided GET parameter
$projectID = isset($_GET["projectID"]) ? $_GET["projectID"] : -1;
$selectedProject = $projectDatabaseHandler->query($_GET["projectID"]);

// if no GET parameter has been set, choose the first project in the project database and redirect to its id
if(!$selectedProject) {
    $selectedProject = $projectDatabaseHandler->queryFirst();
    header("Location: /?projectID={$selectedProject["id"]}");
}
else {
    // create new TestSessionDataDatabaseHandler instance
    $testSessionDataDatabaseHandler = new TestSessionDataDatabaseHandler();

    // query min and max rssi and snr values
    $rssiBounds = $testSessionDataDatabaseHandler->queryRSSI_Bounds($selectedProject["id"]);
    $snrBounds  = $testSessionDataDatabaseHandler->querySNR_Bounds($selectedProject["id"]);

    // prepare the dynamic parts of the website
    // create dropdown for the test configurations
    $testFilter = "";
    $testFilter.= "
    
            <div class='accordion-container'>
                <h3 class='no-select'>Antenna configuration<span class='caret'></span></h3>
                <div id='antennaConfigurations' class='content'>
    
    
    ";

    // create new $testDatabaseHandler instance
    $testDatabaseHandler = new TestDatabaseHandler();
    $tests = $testDatabaseHandler->queryAllByProjects($selectedProject["id"]);
    for($i = 0; $i <count($tests); $i++) {
        // add checkable list entries for every test configuration
        $testFilter.="
                    <label for='antennaConfig_{$tests[$i]["id"]}' >
                        <span class='data_rate_checkbox'>{$tests[$i]["antenna"]} 
                            <input type='checkbox' id='antennaConfig_{$tests[$i]["id"]}'  name='antennaConfig_{$tests[$i]["id"]}'  checked>
                            <span class='checked_indicator'>
                            <span class='check_mark'>
                        </span>
                    </label>
        ";
    }

    $testFilter.="
    
                </div> <!-- content -->
            </div> <!-- accordion-container -->
    
    ";

    // create project chooser dropdown if there is more than one project in the project database
    $projectChooserDropdown = "";
    $projects = $projectDatabaseHandler->queryAll();
    if(count($projects) > 1) {
        $projectChooserDropdown .= "<span class='caret'></span>
                                    <div class='project-drop-down'>";

        foreach ($projects as $project) {
            if($project["id"] !== $selectedProject["id"]) {
                $projectChooserDropdown.= "<a class='muted-link' href='/?projectID={$project['id']}' style='display:block;'>{$project['description']}</a>";
            }
        }
        $projectChooserDropdown .= " </div>";
    }

    $mapsKey = LoRaWanMapperConfig::MAPS_KEY;

    echo "
    <!doctype html>
    <html>
        <head>
            <title>LoRaWan Mapper</title>
            <meta name='viewport' content='initial-scale=1.0'>
            <meta charset='utf-8'>
            <meta name='theme-color' content='#000000'>
            <link rel='stylesheet' href='/css/style.css'>
            <link href='https://fonts.googleapis.com/css?family=Roboto:300,400' rel='stylesheet'>
            <link rel='shortcut icon' type='image/x-icon' href='/img/favicon.ico'>
            <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
            <script src='js/index.js'></script>
            <script src='js/utils.js'></script>
        </head>
        
        <body>
            <div class='title'>
                <h1 class='no-select'>
                    Project: 
                    <div style='' class='description'>{$selectedProject["description"]} 
                    
                       {$projectChooserDropdown}
                        
                    </div>
                </h1>
                <a id='login' class='muted-link' href='/php/Backend.php'>Login</a>
            </div>
            <div class='row'>
                 <div id='map' data-lat='{$selectedProject["latitude"]}' data-lng='{$selectedProject["longitude"]}'></div>
                 
                <div id='filter'>
                    <h2>Filter</h2>
                     <form>
                        {$testFilter}
                        <div class='accordion-container'>
                            <h3 class='no-select'>Data rate<span class='caret'></span></h3>
                            <div id='dataRates' class='content'>
                                <label for='SF7' ><span class='data_rate_checkbox'>SF7  <input type='checkbox' id='SF7'  name='SF7'  checked><span class='checked_indicator'><span class='check_mark'></span></label>
                                <label for='SF8' ><span class='data_rate_checkbox'>SF8  <input type='checkbox' id='SF8'  name='SF8'  checked><span class='checked_indicator'><span class='check_mark'></span></label>
                                <label for='SF9' ><span class='data_rate_checkbox'>SF9  <input type='checkbox' id='SF9'  name='SF9'  checked><span class='checked_indicator'><span class='check_mark'></span></label>
                                <label for='SF10'><span class='data_rate_checkbox'>SF10 <input type='checkbox' id='SF10' name='SF10' checked><span class='checked_indicator'><span class='check_mark'></span></label>
                                <label for='SF11'><span class='data_rate_checkbox'>SF11 <input type='checkbox' id='SF11' name='SF11' checked><span class='checked_indicator'><span class='check_mark'></span></label>
                                <label for='SF12'><span class='data_rate_checkbox'>SF12 <input type='checkbox' id='SF12' name='SF12' checked><span class='checked_indicator'><span class='check_mark'></span></label>
                            </div>
                        </div>
                                                 
                     
                        <div class='accordion-container expanded'>
                            <h3 class='no-select'>Rssi 
                                <span class='value_wrapper'>
                                    (min: <span class='value'>{$rssiBounds["min"]}</span>dBm)
                                </span> 
                                <span class='caret'></span>
                            </h3>
                            <div class='content'>
                                <div class='sliderWrapper row'>
                                    <span>{$rssiBounds["min"]}dBm</span></span><input type='range' min='{$rssiBounds["min"]}' max='{$rssiBounds["max"]}' value='{$rssiBounds["min"]}' step='1' id='rssiSlider' class='slider'><span>{$rssiBounds["max"]}dBm</span>
                                </div>
                            </div>
                        </div>        
                        
                        <div class='accordion-container expanded'>
                             <h3 class='no-select'>Snr 
                                <span class='value_wrapper'>
                                    (min: <span class='value'>{$snrBounds["min"]}</span>dBm)
                                </span> 
                                <span class='caret'></span>
                            </h3>
                            <div class='content'>
                                <div class='sliderWrapper row'>
                                    <span>{$snrBounds["min"]}dB</span></span><input type='range' min='{$snrBounds["min"]}' max='{$snrBounds["max"]}' value='{$snrBounds["min"]}' step='0.1' id='snrSlider' class='slider'><span>{$snrBounds["max"]}dB</span>
                                </div>
                            </div>
                                                
                        </div>
                        <button id='sync' title='sync'><img src='img/sync.png' </button>  
                    </form>
                 </div>
            </div>
           
            <script src='https://maps.googleapis.com/maps/api/js?key={$mapsKey}&callback=initMap' async defer></script>
        </body>
    
    
    </html>";
}