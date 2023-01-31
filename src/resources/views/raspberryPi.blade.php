<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <title>{{ __('messages.layout_names.raspberry_pi') }} | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico"/>
    <link href="{{ asset('css/front.css') }}" rel="stylesheet"/>
    <script src="{{ asset('js/front.js') }}" defer></script>
    <script src="{{ asset('js/charts.js') }}" defer></script>
    <script src="{{ asset('js/raspberryPi.js') }}" defer></script>
</head>
<body>
@include('layouts.nav')
<section class="mt-5">
    @include('layouts.error')
</section>
<section class="options-container">
    <section class="p-2 flex-fill form-container">
        <form class="map-form" action="/">
            <section class="form-last-reading-section input-group-prepend">
                <label class="justify-content-center span input-group-text invisible"
                       for="generatedHeader">Headers</label>
                <input class="input" type="text" value="" name="generatedHeader" id="generatedHeader"
                       disabled="disabled" placeholder="Producido"/>
                <label for="consumedHeader"></label>
                <input class="input" type="text" value="" name="consumedHeader" id="consumedHeader"
                       disabled="disabled" placeholder="Consumido Red"/>
                <label for="producedHeader"></label>
                <input class="input" type="text" value="" name="producedHeader" id="producedHeader"
                       disabled="disabled" placeholder="Consumido Solar"/>
                <label for="injectedHeader"></label>
                <input class="input" type="text" value="" name="injectedHeader" id="injectedHeader"
                       disabled="disabled" placeholder="Volcado Red"/>
{{--                <label for="powerHeader"></label>--}}
{{--                <input class="input" type="text" value="" name="powerHeader" id="powerHeader"--}}
{{--                       disabled="disabled" placeholder="Potencia"/>--}}
            </section>
            <section class="form-last-reading-section input-group-prepend">
                <label class="justify-content-center span input-group-text" for="generated">Total Energía</label>
                <input class="input" type="text" value="" name="generated" id="generated" disabled="disabled"/>
                <label for="consumed"></label>
                <input class="input" type="text" value="" name="consumed" id="consumed"
                       disabled="disabled"/>
                <label for="produced"></label>
                <input class="input" type="text" value="" name="produced" id="produced" disabled="disabled"/>
                <label for="injected"></label>
                <input class="input" type="text" value="" name="injected" id="injected"
                       disabled="disabled"/>
            </section>
{{--            <section class="form-distance-section input-group-prepend">--}}
{{--                <label class="justify-content-center span input-group-text" for="minValueLeft">Total Coste</label>--}}
{{--                <input class="input" type="text" value="" name="minValueLeft" id="minValueLeft" disabled="disabled"/>--}}
{{--                <label for="minValueCenter"></label>--}}
{{--                <input class="input" type="text" value="" name="minValueCenter" id="minValueCenter"--}}
{{--                       disabled="disabled"/>--}}
{{--                <label for="minValueRight"></label>--}}
{{--                <input class="input" type="text" value="" name="minValueRight" id="minValueRight" disabled="disabled"/>--}}
{{--                <label for="powerCost"></label>--}}
{{--                <input class="input" type="text" value="" name="powerCost" id="powerCost" disabled="disabled"/>--}}
{{--            </section>--}}
            <section class="form-date-section input-group-prepend">
                <label class="justify-content-center span input-group-text" for="startDate">Date</label>
                <label class="d-none" for="endDate"></label>
                <input class="input" type="date" value="" name="startDate" id="startDate"/>
                <input class="input" type="date" value="" name="endDate" id="endDate"/>
                <button class="span btn btn-outline-secondary" type="button" onclick="retrieveSensorData()">
                    Calcular
                </button>
                {{--                <a href="#" class="span btn btn-outline-secondary"--}}
                {{--                   onclick="window.open('Solar panels.pdf', '_blank', 'fullscreen=yes'); return false;">--}}
                {{--                    Show PDF--}}
                {{--                </a>--}}
            </section>
        </form>
    </section>
    <section class="p-2 flex-fill map-container">
        <div id="map" class="chart"></div>
    </section>
</section>
<div id="voltageChart" class="chart"></div>
<div id="batteryChart" class="chart"></div>
<script src="{{ asset('js/front-defer.js') }}" defer></script>
</body>
@include('layouts.footer')
@include('layouts.spinner')
</html>