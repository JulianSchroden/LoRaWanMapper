<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

class CreateTestView {

    /**
     * Creates layout to create a new test and returns it as a string
     * @return string
     */
    public static function create() {
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
                    <link rel='stylesheet' href='/css/create_project_style.css'>
                    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
                    <script src='/js/backend.js'></script>
                    <script src='/js/utils.js'></script>
                </head>
            
            
            
                <body>
                    <div class='title'>
                         <span class='backArrow'>
                            <img src='/img/arrow-left.png' alt='back' width='36' height='36' style='margin:14px;'>
                        </span>
                        <h1>new Test</h1>
                    </div>
                   
                    <div id='createTestView' class='content'>
                        <form method='post' id='form-recaptcha'>
                            <div class='input-wrapper'>
                                <input name='antennaModel' type='text' placeholder='Antenna model'>
                            </div>
                            
                            <div class='input-wrapper'>
                                <button type='submit'>Create Test</button>                  
                            </div>
                        </form>                      
                        
                        
                    </div>
                </body>
            </html>
        ";
    }
}
