<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <title>{{ __('messages.layout_names.dashboard') }} | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/favicon.ico') }}">
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
                <input class="input" type="text"
                       value="@if(isset($dynamoDbData['deviceId1']['lastReading'])) $dynamoDbData['deviceId1']['lastReading']; @endif"
                       name="from1" id="from1" disabled="disabled"/>
                <label for="from2"></label>
                <input class="input" type="text"
                       value="@if(isset($dynamoDbData['deviceId2']['lastReading'])) $dynamoDbData['deviceId2']['lastReading']; @endif"
                       name="from2" id="from2" disabled="disabled"/>
            </section>
            <section class="form-date-section input-group-prepend">
                <label class="justify-content-center span input-group-text"
                       for="from">{{ __('messages.polylines_layout.date') }}</label>
                <input class="input" type="date" value="" name="from" id="from"
                       onchange="submitForm()"/>
                <div class="container input">
                    <div class="row">
                        <div class="col-3 mx-0 p-0">
                            <label class="justify-content-center input-group-text" for="startHours">
                                {{ __('messages.polylines_layout.from') }}
                            </label>
                        </div>
                        <div class="col-3 mx-0 p-0">
                            <input class="input checkbox-medium mx-0 p-1" type="number" name="startHours"
                                   id="startHours" max="23"
                                   min="0"
                                   onchange="submitForm()"/>
                        </div>
                        <div class="col-3 mx-0 p-0">
                            <label class="justify-content-center input-group-text" for="endHours">
                                {{ __('messages.polylines_layout.to') }}
                            </label>
                        </div>
                        <div class="col-3 mx-0 p-0">
                            <input class="input checkbox-medium mx-0 p-1" type="number" name="endHours" id="endHours"
                                   max="23" min="0"
                                   onchange="submitForm()"/>
                        </div>
                    </div>
                </div>
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
                <label for="readingsCount"></label>
                <input class="input" type="text" value="{{ __('messages.polylines_layout.readings') }}"
                       name="readingsCount" id="readingsCount" disabled="disabled"/>
            </section>
            <section class="form-device-section input-group-prepend">
                <span class="justify-content-center span input-group-text">{{ __('messages.polylines_layout.device_name') }}</span>
                <label for="deviceId1Select"></label>
                <input class="select input" list="devicesList1" id="deviceId1Select" name="deviceId1"
                       onchange="submitForm()">
                <datalist id="devicesList1">
                    @foreach($devicesData as $companyName => $vehicles)
                        <optgroup label={{ $companyName }}>
                            @foreach($vehicles as $vehicleId => $devices)
                                <optgroup label="&nbsp;&nbsp;{{ $vehicleId }}">
                                    @foreach($devices as $deviceInfo)
                                        <option value="{{ $deviceInfo->device_id }}">
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $companyName }}-{{ $vehicleId }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </optgroup>
                    @endforeach
                </datalist>
                <label for="deviceId2Select"></label>
                <input class="select input" list="devicesList2" id="deviceId2Select" name="deviceId2"
                       onchange="submitForm()">
                <datalist id="devicesList2">
                    @foreach($devicesData as $companyName => $vehicles)
                        <optgroup label={{ $companyName }}>
                            @foreach($vehicles as $vehicleId => $devices)
                                <optgroup label="&nbsp;&nbsp;{{ $vehicleId }}">
                                    @foreach($devices as $deviceInfo)
                                        <option value="{{ $deviceInfo->device_id }}">
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $companyName }}-{{ $vehicleId }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </optgroup>
                    @endforeach
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
<section class="mt-5">
    @include('layouts.error')
</section>
<div id="tempChart" class="chart mt-4"></div>
<div id="pressureChart" class="chart mt-3"></div>
<div id="extraData" class="justify-content-center mx-auto mt-5 d-none d-md-block"></div>
<div class="mt-3">&nbsp;</div>
<script async="async" defer="defer"
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>
<script src="{{ asset('js/front-defer.js') }}" defer></script>
</body>
@include('layouts.footer')
@include('layouts.spinner')
</html>