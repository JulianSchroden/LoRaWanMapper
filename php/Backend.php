<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */


require_once "utils.php";
require_once "config/LoRaMapperConfig.php";
session_start();


$captchaSecret = LoRaMapperConfig::CAPTCHA_KEYS["private"];
$userName      = LoRaMapperConfig::BACKEND_LOGIN["userName"];
$password      = LoRaMapperConfig::BACKEND_LOGIN["password"];



// unset session, if logout parameter is set
if(isset($_GET["logout"])) {
    unset($_SESSION["userID"]);
}



// if no user is logged in and a userName and password is transferred within $_Post, check if credentials are correct
if(!isset($_SESSION["userID"]) && isset($_POST["userName"]) && isset($_POST["password"]) && isset($_POST["g-recaptcha-response"])) {
    if (checkCaptcha($captchaSecret, $_POST["g-recaptcha-response"])) {
        if ($_POST["userName"] == $userName && $_POST["password"] == $password) {
            $_SESSION["userID"] = "userName";  // set session
        } else {
            unset($_SESSION["userID"]);
        }
    }
}


// if user is logged in, echo requested view
if(isset($_SESSION["userID"])) {
    switch($_GET["page"]) {
        case "createProject":
            require_once "views/CreateProjectView.php";
            echo CreateProjectView::create();
            return;
        case "test":
            require_once "views/TestsOverviewView.php";
            if(isset($_GET["id"])) {
                echo TestsOverviewView::create($_GET["id"]);
                return;
            }
            break;
        case "createTest":
            require_once "views/CreateTestView.php";
            if(isset($_GET["projectID"])) {
                echo CreateTestView::create();
                return;
            }
            break;
    }
    // default page
    require_once "views/ProjectsOverviewView.php";
    echo ProjectsOverviewView::create();
}

else {
    // show login view
    require_once "views/LoginView.php";
    echo LoginView::create();
}
