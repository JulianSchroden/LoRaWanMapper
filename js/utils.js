/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

const DEBUGG    = false;

/**
 * helper function to communicate with server
 * @param data     reference to FormData object
 * @param target   the target url
 * @param callback optional callback function, which will be called with the received data
 */

function communicateWithServer(data, target, callback){
    callback = callback || function () {};
    let request = new XMLHttpRequest();
    request.open("POST",target);
    request.addEventListener('load', function(event) {
        if (request.status >= 200 && request.status < 300) {
            if(DEBUGG)
                console.log("response= " +request.responseText);
            callback(request.responseText);
        } else {
            if(DEBUGG)
                console.warn(request.statusText, request.responseText);
        }
    });
    request.send(data);
}
