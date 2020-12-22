<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
            crossorigin="anonymous"></script>
    <link rel="icon" type="image/x-icon" href="/favicon.ico"/>
    <link href="{{ asset('css/front.css') }}" rel="stylesheet"/>
    <script src="{{ asset('js/front.js') }}" defer></script>
    <script src="{{ asset('js/charts.js') }}" defer></script>
    <script src="{{ asset('js/map.js') }}" defer></script>
</head>
<body onload="initMap()">
@include('layouts.nav')
<section class="options-container">
    <section class="p-2 flex-fill form-container">
        <form class="map-form" action="/">
            <section class="form-last-reading-section input-group-prepend">
                <label class="justify-content-center span input-group-text"
                       for="from1">{{ __('messages.polylines_layout.previous_reading') }}</label>
                <input class="input" type="text" value="<?php // echo $dynamoDbData['deviceId1']['lastReading']; ?>"
                       name="from1" id="from1" disabled="disabled"/>
                <label for="from2"></label>
                <input class="input" type="text" value="<?php // echo $dynamoDbData['deviceId2']['lastReading']; ?>"
                       name="from2" id="from2" disabled="disabled"/>
            </section>
            <section class="form-date-section input-group-prepend">
                <label class="justify-content-center span input-group-text"
                       for="from">{{ __('messages.polylines_layout.date') }}</label>
                <input class="input" type="date" value="<?php // echo $date; ?>" name="from" id="from"
                       onchange="submitForm()"/>
                <span class="justify-content-center input-checkbox">
                    <label for="numHours"></label>
                    <input class="input checkbox-wide" type="number" name="numHours" id="numHours" max="24" min="1"
                           onchange="submitForm()"/>
                    {{ __('messages.polylines_layout.hours') }}
                </span>
            </section>

            <section class="form-date-section input-group-prepend">
                <span class="justify-content-center span input-group-text">{{ __('messages.polylines_layout.data') }}</span>
                <span class="justify-content-center input-checkbox d-none">
                    <label for="pressureInBars"></label>
                    <input class="input checkbox" type="checkbox" name="pressureInBars" id="pressureInBars"
                           onchange="submitForm()"/>
                    {{ __('messages.polylines_layout.pressure_in_bars') }}
                </span>
                <label for="tableNameSelect"></label>
                <input class="select input" list="tableNames" id="tableNameSelect" name="tableNames"
                       onchange="submitForm()" placeholder="Table Name">
                <datalist id="tableNames">
                    <option value="DevicesRichDataTable"></option>
                    <option value="TestRichDataTable"></option>
                </datalist>
                <span class="justify-content-center input-checkbox">
                    <label for="lastHour"></label>
                    <input class="input checkbox" type="checkbox" name="lastHour" id="lastHour"
                           onchange="submitForm()"/>
                    {{ __('messages.polylines_layout.last_hour') }}
                </span>
            </section>
            <section class="form-device-section input-group-prepend">
                <span class="justify-content-center span input-group-text">{{ __('messages.polylines_layout.device_name') }}</span>
                <label for="deviceId1Select"></label>
                <input class="select input" list="devicesList1" id="deviceId1Select" name="deviceId1"
                       onchange="submitForm()">
                <datalist id="devicesList1">
                    <?php
                    // printDevices($deviceData);
                    ?>
                </datalist>
                <label for="deviceId2Select"></label>
                <input class="select input" list="devicesList2" id="deviceId2Select" name="deviceId2"
                       onchange="submitForm()">
                <datalist id="devicesList2">
                    <?php
                    // printDevices($deviceData);
                    ?>
                </datalist>
            </section>
            <section class="form-distance-section input-group-prepend">
                <label class="justify-content-center span input-group-text"
                       for="distance1">{{ __('messages.polylines_layout.travelled_distance') }}</label>
                <input class="input" type="text" value="" name="distance1" id="distance1" disabled="disabled"/>
                <label for="distance2"></label>
                <input class="input" type="text" value="" name="distance2" id="distance2" disabled="disabled"/>
            </section>
        </form>
    </section>
    <section class="p-2 flex-fill map-container">
        <div id="map" class="chart"></div>
    </section>
</section>
<div id="tempChart" class="chart"></div>
<div id="pressureChart" class="chart"></div>
<div id="voltageChart" class="chart"></div>
<div id="extraData" class="justify-content-center mx-auto mt-5"></div>
<script async="async" defer="defer"
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>
</body>
@include('layouts.spinner')
</html>