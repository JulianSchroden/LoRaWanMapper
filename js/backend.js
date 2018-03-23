/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

let gatewayLocation = {lat:0, lng:0};

$(function () {


    $("#newProjectFAB").on("click", function() {
       // redirect to createProject page
       window.location= "?page=createProject";
    });

    $("#newTestFAB").on("click", function() {
        let getQuery  = new URLSearchParams(window.location.search);
        let projectID = getQuery.get("id");
        if(projectID) {
            // redirect to create test page
            window.location= "?page=createTest&projectID="+projectID;
        }
    });

    // create project page
    $("#createProjectView button[type=submit]").on("click", function (event) {
        event.preventDefault();
        event.stopPropagation();
        console.log("lallala");

        let projectDescription=$("input[name=projectDescription]").val().trim();
        let gatewayModel = $("input[name=gatewayModel]").val().trim();
        let gatewayID = $("input[name=gatewayID]").val().trim();

        if(projectDescription !== "" && gatewayModel !== "" && gatewayID !== "" && !(gatewayLocation.lat === 0 && gatewayLocation.lng ===0)){
           let formData = new FormData();
           formData.append("option", "add");
           formData.append("projectDescription", projectDescription);
           formData.append("gatewayModel", gatewayModel);
           formData.append("gatewayID", gatewayID);
           formData.append("gatewayLocation", JSON.stringify(gatewayLocation));

           communicateWithServer(formData, "/php/Manager/ProjectManager.php", function(result){
               if(result === "success"){
                   window.location = "/php/Backend.php";
               }
           });
        }
    });


    // create test page
    $("#createTestView button[type=submit]").on("click", function (event) {
        event.preventDefault();
        event.stopPropagation();

        let antennaModel = $("input[name=antennaModel]").val().trim();

        let getQuery = new URLSearchParams(window.location.search);
        let projectID = getQuery.get("projectID");

        if(antennaModel !== "" && projectID !== ""){
            let formData = new FormData();
            formData.append("option", "add");
            formData.append("antennaModel", antennaModel);
            formData.append("projectID", projectID);


            communicateWithServer(formData, "/php/Manager/TestManager.php", function(result){
                if(result === "success"){
                    window.location = "/php/Backend.php?page=test";
                }
            });
        }
    });

    // tests overview page
    $("#testsOverview input[type=radio]").on("click", function (event) {
        let testID = $(event.target).attr("data-id");
        console.log("activate test "+testID);

        let formData = new FormData();
        formData.append("option", "setActive");
        formData.append("activeTestID", testID);


        communicateWithServer(formData, "/php/Manager/TestManager.php", function(result){
          /*  if(result === "success"){

            }
          */
        });

    });


    // take care of back navigation
    $(".backArrow").on("click",function () {
        let getQuery = new URLSearchParams(window.location.search);
        let page = getQuery.get("page");
        let path = "/php/Backend.php";

        if(page === "createTest") {
           let id = getQuery.get("projectID");
           path = "/php/Backend.php?page=test&id="+id;
        }
        window.location = path;
    });

});


// callback for the maps api create project page
function initMap() {
    let gatewayPosition={lat: 50.039519, lng: 6.861197};
    let map = new google.maps.Map(document.getElementById('map'), {
        center: gatewayPosition,
        zoom: 5,
        scaleControl: true,
        streetViewControl: false
    });

    let marker = new google.maps.Marker({
        draggable: true,
        map: map,
        title: "Your location"
    });

    google.maps.event.addListener(marker, 'dragend', function(event) {
        gatewayLocation.lat = event.latLng.lat();
        gatewayLocation.lng = event.latLng.lng();
        console.log(gatewayLocation);
    });

    google.maps.event.addListener(map, 'click', function(event) {
        gatewayLocation.lat = event.latLng.lat();
        gatewayLocation.lng = event.latLng.lng();
        console.log(gatewayLocation);
        marker.setPosition(event.latLng);
    });

}
