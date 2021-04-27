<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <title>{{ __('messages.layout_names.raspberry_pi') }} | {{ config('app.name') }}</title>
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
    <script src="{{ asset('js/raspberryPi.js') }}" defer></script>
</head>
<body>
@include('layouts.nav')
<section class="options-container">
    <section class="p-2 flex-fill form-container">
        <form class="map-form" action="/">
            <section class="form-last-reading-section input-group-prepend">
                <label class="justify-content-center span input-group-text invisible"
                       for="maxValueLeftHeader">Headers</label>
                <input class="input" type="text" value="" name="maxValueLeftHeader" id="maxValueLeftHeader"
                       disabled="disabled" placeholder="Total"/>
                <label for="maxValueCenterHeader"></label>
                <input class="input" type="text" value="" name="maxValueCenterHeader" id="maxValueCenterHeader"
                       disabled="disabled" placeholder="Consumido"/>
                <label for="maxValueRightHeader"></label>
                <input class="input" type="text" value="" name="maxValueRightHeader" id="maxValueRightHeader"
                       disabled="disabled" placeholder="Producido"/>
                <label for="powerHeader"></label>
                <input class="input" type="text" value="" name="powerHeader" id="powerHeader"
                       disabled="disabled" placeholder="Potencia"/>
            </section>
            <section class="form-last-reading-section input-group-prepend">
                <label class="justify-content-center span input-group-text" for="maxValueLeft">Total Energía</label>
                <input class="input" type="text" value="" name="maxValueLeft" id="maxValueLeft" disabled="disabled"/>
                <label for="maxValueCenter"></label>
                <input class="input" type="text" value="" name="maxValueCenter" id="maxValueCenter"
                       disabled="disabled"/>
                <label for="maxValueRight"></label>
                <input class="input" type="text" value="" name="maxValueRight" id="maxValueRight" disabled="disabled"/>
                <label for="powerContracted"></label>
                <input class="input" type="text" value="" name="powerContracted" id="powerContracted"
                       disabled="disabled"/>
            </section>
            <section class="form-distance-section input-group-prepend">
                <label class="justify-content-center span input-group-text" for="minValueLeft">Total Coste</label>
                <input class="input" type="text" value="" name="minValueLeft" id="minValueLeft" disabled="disabled"/>
                <label for="minValueCenter"></label>
                <input class="input" type="text" value="" name="minValueCenter" id="minValueCenter"
                       disabled="disabled"/>
                <label for="minValueRight"></label>
                <input class="input" type="text" value="" name="minValueRight" id="minValueRight" disabled="disabled"/>
                <label for="powerCost"></label>
                <input class="input" type="text" value="" name="powerCost" id="powerCost" disabled="disabled"/>
            </section>
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