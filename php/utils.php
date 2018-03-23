<?php
/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

/**
 * checks if captcha has been triggered "by a human"
 * @param $secret
 * @param $captchaResponse
 * @return boolean
 */
function checkCaptcha($secret, $captchaResponse) {
    /**
        response=object(stdClass)#1 (3) {
            ["success"]=>bool(true)
            ["challenge_ts"]=>string(20) "2017-10-12T17:29:33Z"
            ["hostname"]=>string(19) "example.com"
        }
     */
    $verify       = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$captchaResponse}");
    $captcha_data = json_decode($verify);
    return $captcha_data->success;
}