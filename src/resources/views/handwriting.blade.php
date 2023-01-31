<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <title>{{ __('messages.layout_names.handwriting') }} | {{ config('app.name') }}</title>
    <script src='https://unpkg.com/tesseract.js@v2.1.0/dist/tesseract.min.js'></script>
    <link rel="icon" type="image/x-icon" href="/favicon.ico"/>
    <link href="{{ asset('css/front.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/handwriting.css') }}" rel="stylesheet"/>
    <script src="{{ asset('js/front.js') }}" defer></script>
</head>
<body onload="init()">
@include('layouts.nav')
<div id="sketchpadapp">
    <div class="leftside text-center">
        Touchscreen and mouse support HTML5 canvas sketchpad.
        <br/><br/>
        Draw something by tapping or dragging.
        <br/><br/>
        Works on iOS, Android and desktop/laptop touchscreens using Chrome/Firefox/Safari.
        <br/><br/>
        <br>
        <br>
        <br>
        <input type="radio" name="pencilColour" value="black" checked>
        <label for="black">Black</label>
        <input type="radio" name="pencilColour" value="red">
        <label for="red">Red</label>
        <input type="radio" name="pencilColour" value="blue">
        <label for="white">Blue</label>
        <br>
        <input type="radio" name="pencilColour" value="green">
        <label for="green">Green</label>
        <input type="radio" name="pencilColour" value="yellow">
        <label for="yellow">Yellow</label>
        <input type="radio" name="pencilColour" value="white">
        <label for="white">White</label>
        <br>
        <input type="radio" name="pencilColour" value="rubber">
        <label for="blue">Rubber</label>
        <button class="btn btn-outline-danger" onclick="clearCanvas(canvas,ctx);">
            Clear Sketchpad
        </button>
    </div>
    <div class="rightside">
        <canvas id="sketchpad" height="600" width="1024">
        </canvas>
    </div>
</div>
<button id="img-to-txt" class="btn btn-outline-info">
    Convert image to text
</button>
<div id="ocr_results"></div>
<div id="ocr_status"></div>
<!-- <script src="js/webPush.js"></script> -->
<p>Current light intensity is <b id="value">unknown</b>.</p>
<div id="box"></div>
<button class="btn btn-outline-info">
    Enable Push Messages
</button>
<!--  <script src="/js/saveCanvas.js"></script>-->
<script src="{{ asset('js/handwriting.js') }}"></script>
<script src="{{ asset('js/front-defer.js') }}" defer></script>
</body>
@include('layouts.footer')
@include('layouts.spinner')
</html>