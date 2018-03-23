/**
 *  Copyright (c) 2017-2018 Julian Schroden. All rights reserved.
 *  Licensed under the MIT License. See LICENSE file in the project root for full license information.
 */

const QUERY_URL = "/php/query.php";


let map; // google maps instance
let geotaggedSignals=[]; // array containing all the project data and markers

// callback for the maps api
function initMap() {
    // create a new gogle.maps.Map instance
    map = new google.maps.Map(document.getElementById('map'), {
        center: gatewayPosition,
        zoom: 14,
        scaleControl: true,
        streetViewControl: false
    });

    // place a marker on the map which represents the gateway's location on the map
    let gatewayPosition={
        lat: parseFloat($("#map").attr("data-lat")),
        lng: parseFloat($("#map").attr("data-lng"))
    };
    let gatewayMarker = new google.maps.Marker({
        position : gatewayPosition,
        map      : map,
        title    : "Gateway"
    });
}




window.onload=function () {
    // fetch data from database and apply filter
    fetchData();

    // handle project chooser dropdown
    let projectChooserDropdown = $(".title .project-drop-down");
    if(projectChooserDropdown) {
        let projectDescription = $(".title .description");
        projectDescription.on("click", function (event) {
            if(projectChooserDropdown.css("display") === "none") {
                // show projectChooser dropdown
                let height = (projectDescription.height() + 10)+"px";
                projectChooserDropdown.css("top", height);
                projectChooserDropdown.css("display", "block");
                projectChooserDropdown.parent().addClass("expanded");
            }
            else {
                // hide projectChooser dropdown
                projectChooserDropdown.css("display", "none");
                projectChooserDropdown.parent().removeClass("expanded");
            }
        });
    }

    // add event listener to refresh button
    let refreshButton=document.querySelector("#sync");
    refreshButton.addEventListener("click",function (event) {
        event.preventDefault();
        event.stopPropagation();
        // fetch data from database and apply filter
        fetchData();
    });


    function fetchData() {
        let getQuery = new URLSearchParams(window.location.search);
        let projectID = getQuery.get("projectID"); // get project ID

        let data=new FormData();
        data.append("option", "fetch");
        data.append("projectID", projectID);


        communicateWithServer(data, QUERY_URL, function (data) {

            // remove all old markers
            for (let testName in geotaggedSignals) {
                if (geotaggedSignals.hasOwnProperty(testName)) {

                    let testGroup = geotaggedSignals[testName];
                    for (let dataRateName in testGroup){
                        if (testGroup.hasOwnProperty(dataRateName)){

                            let dataRateGroup = testGroup[dataRateName];
                            dataRateGroup.forEach(function(signal, index){
                                geotaggedSignals[testName][dataRateName][index].marker.setMap(null);
                            });
                        }
                    }
                }
            }

            // parse received data
            geotaggedSignals = JSON.parse(data);

            if(geotaggedSignals){
                let colors={
                    1  : "#0B9C30",
                    2  : "#ffc300",
                    3  : "#c5150e",
                    4  : "#0059ff",
                    5  : "#6300dd",
                    6  : "#c8ff00"
                };

                // vars needed to filter the data
                let minRSSI = $("#rssiSlider").val();
                let minSNR  = $("#snrSlider").val();

                for (let testName in geotaggedSignals) {
                    if (geotaggedSignals.hasOwnProperty(testName)) {
                        let testGroup = geotaggedSignals[testName];

                        let circle = {
                            path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
                            fillColor: colors[testName],
                            fillOpacity: .5,
                            anchor: new google.maps.Point(0,0),
                            strokeWeight: 0,
                            scale: .75
                        };


                        for (let dataRateName in testGroup){
                            if (testGroup.hasOwnProperty(dataRateName)){
                                let dataRateGroup = testGroup[dataRateName];

                                dataRateGroup.forEach(function(signal, index){
                                    // assign map if data fulfills filter, else null to hide marker
                                    let tempMap = null;
                                    if($("#antennaConfigurations input[name='antennaConfig_"+testName+"']").is(":checked") && $("#dataRates input[name='"+dataRateName+"']").is(":checked") && signal.snr >= minSNR && signal.rssi >= minRSSI) {
                                        tempMap = map;
                                    }

                                    // create new google.maps.Marker instances
                                    geotaggedSignals[testName][dataRateName][index].marker = new google.maps.Marker({
                                        position : {lat:signal.latitude, lng: signal.longitude},
                                        map      : tempMap,
                                        title    : "counter: " + signal.counter + ", dataRate: " + signal.data_rate + ", rssi: " + signal.rssi+", snr: " + signal.snr,
                                        icon     : circle
                                    });
                                });
                            }
                        }
                    }
                }
            }
        });
    }

    function applyFilter() {
        let minRSSI = $("#rssiSlider").val();
        let minSNR  = $("#snrSlider").val();

        for (let testName in geotaggedSignals) {
            if (geotaggedSignals.hasOwnProperty(testName)) {
                let testGroup = geotaggedSignals[testName];

                for (let dataRateName in testGroup) {
                    if (testGroup.hasOwnProperty(dataRateName)) {
                        let dataRateGroup = testGroup[dataRateName];

                        dataRateGroup.forEach(function(signal, index) {
                            // assign map if data fulfills filter, else null to hide marker
                            let tempMap = null;
                            if($("#antennaConfigurations input[name='antennaConfig_"+testName+"']").is(":checked") && $("#dataRates input[name='"+dataRateName+"']").is(":checked") && signal.snr >= minSNR && signal.rssi >= minRSSI) {
                                tempMap = map;
                            }
                            if(geotaggedSignals[testName][dataRateName][index].marker.getMap() !== tempMap) {
                                geotaggedSignals[testName][dataRateName][index].marker.setMap(tempMap);
                            }
                        });
                    }
                }
            }
        }
    }

    // apply filter
    $("#dataRates input[type=checkbox], #antennaConfigurations input[type=checkbox]").on("click",function () {
        applyFilter();
    });

    $(".slider").on("mouseup touchend",function () {
        applyFilter();
    });

    $(".accordion-container>h3").on("click",function () {
        $(this).parent().toggleClass("expanded");
    });


    // update slider preview value
    $(".slider").on("input",function() {
       $(this).closest(".accordion-container").find(".value").text(this.value);
    });

};
