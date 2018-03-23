<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

require_once __DIR__."/../config/LoRaMapperConfig.php";

class LoginView {

    /**
     * Creates login layout and returns it as a string
     * @return string
     */
    public static function create() {

        $captchaKey = LoRaMapperConfig::CAPTCHA_KEYS["public"];
        return "

        <!doctype html>
        <html lang='de'>
            <head>
                <meta name='viewport' content='width=device-width, initial-scale=1'>
                <meta charset='utf-8'>
                <title>Backend login</title>
                <meta name='theme-color' content='#000000'>
                <link rel='stylesheet' href='/css/style.css'>
                <link rel='stylesheet' href='/css/login_style.css'>
                <script src='https://www.google.com/recaptcha/api.js' async defer></script> 
            </head>
        
        
        
            <body>
                <div class='linkToStartPage'>
                    <a class='muted-link' href='/'>Home</a>
                </div>
                <div class='centered'>
                    <form method='post' action='Backend.php' id='form-recaptcha'>
                        <div class='input-wrapper'>
                            <input name='userName' type='text' class='form-control' id='userName' placeholder='Name'>
                        </div>
                        <div class='input-wrapper'>
                            <input name='password' type='password' class='form-control' id='InputPassword' placeholder='Password'>
                        </div>
        
                        <div class='input-wrapper'>
                            <div class='g-recaptcha' data-sitekey='{$captchaKey}'></div>
                        </div>
                        <div class='input-wrapper'>
                            <button type='submit'>Sign in</button>                  
                        </div>
                    </form>
                </div>
            </body>
        </html>

    ";
    }
}