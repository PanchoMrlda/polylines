<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Tesseract Example</title>
  <!-- <script src='https://unpkg.com/tesseract.js@v2.0.0-beta.1/dist/tesseract.min.js'></script> -->
  <script src="https://cdn.rawgit.com/naptha/tesseract.js/0.2.0/dist/tesseract.js"></script>
  <link rel="stylesheet" href="css/handwriting.css" />
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" /> -->
</head>

<body onload="init()">
  <div id="sketchpadapp">
    <!-- <i class="fa fa-pencil active" style="font-size:24px"></i>
    <i class="fa fa-eraser" style="font-size:24px"></i> -->
    <div class="leftside">
      Touchscreen and mouse support HTML5 canvas sketchpad.<br /><br />
      Draw something by tapping or dragging.<br /><br />
      Works on iOS, Android and desktop/laptop touchscreens using Chrome/Firefox/Safari.<br /><br />
      <input type="submit" value="Clear Sketchpad" id="clearbutton" onclick="clearCanvas(canvas,ctx);" />
    </div>
    <div class="rightside">
      <canvas id="sketchpad" height="300" width="400">
      </canvas>
    </div>
  </div>
  <button id="img-to-txt">
    Convert image to text
  </button>
  <div id="ocr_results"></div>
  <div id="ocr_status"></div>
  <!-- <script src="js/webPush.js"></script> -->
  <p>Current light intensity is <b id="value">unknown</b>.</p>
  <div id="box"></div>
  <button class="js-push-button">
    Enable Push Messages
  </button>
  <script src="js/handwriting.js"></script>
</body>

</html>