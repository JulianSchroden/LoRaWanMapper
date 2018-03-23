<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once  __DIR__."/../DatabaseHandler/ProjectDatabaseHandler.php";
class ProjectsOverviewView {

    /**
     * Creates projects overview layout and returns it as a string
     * @return string
     */
    public static function create(){
        // create new ProjectDatabaseHandler instance
        $projectDatabaseHandler = new ProjectDatabaseHandler();
        $projectDatabaseHandler->create();

        // query all projects
        $projects = $projectDatabaseHandler->queryAll();

        // create project list entry layouts
        $projectLayouts = "";
        for($i = 0; $i < count($projects); $i++){
            $projectLayouts .= "
                        <div class='project-overview'>
                            <div>
                                 <h2>{$projects[$i]["description"]}</h2>
                                 <div><span class='attribute-name'>Gateway model</span>: {$projects[$i]["gateway_desc"]}</div>
                                 <div><span class='attribute-name'>Gateway ID   </span>: {$projects[$i]["gateway_id"]}</div>
                                 <div><span class='attribute-name'>Location     </span>: <a class='muted-link' href='https://www.google.com/maps/?q={$projects[$i]["latitude"]},{$projects[$i]["longitude"]}'>{$projects[$i]["latitude"]},{$projects[$i]["longitude"]}</a></div>
                            </div>
                            <a href='?page=test&id={$projects[$i]["id"]}' class='test-link muted-link'>Show tests</a>
                        </div>";
        }

        return "
            <!doctype html>
            <html lang='de'>
                <head>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <meta charset='utf-8'>
                    <title>Backend</title>
                    <meta name='theme-color' content='#000000'>
                    <link rel='stylesheet' href='/css/style.css'>
                    <link rel='stylesheet' href='/css/backend_style.css'>
                    <link rel='stylesheet' href='/css/project-overview-style.css'>
                    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
                    <script src='/js/backend.js'></script>

                </head>
            
            
            
                <body>
                    <div class='title'>
                        <h1>Projects</h1>
                        <a class='muted-link' href='?logout'>Logout</a>
                    </div>
                   
                    <div class='content'>
                       
                        
                        {$projectLayouts}
                        
                        
                       <div class='fab' id='newProjectFAB'>
                            <span>+</span>
                        </div>
                    </div>
                    
                </body>
            </html>
        ";
    }
}
