const HANDWRITING_SIZE = 3;
// Variables for referencing the canvas and 2dcanvas context
var canvas, ctx;

// Variables to keep track of the mouse position and left-button status 
var mouseX, mouseY, mouseDown = 0;

// Variables to keep track of the touch position
var touchX, touchY;

// Keep track of the old/last position when drawing a line
// We set it to -1 at the start to indicate that we don't have a good value for it yet
var lastX, lastY = -1;

// Draws a line between the specified position on the supplied canvas name
// Parameters are: A canvas context, the x position, the y position, the size of the dot
function drawLine(ctx, x, y, size) {

  // If lastX is not set, set lastX and lastY to the current position 
  if (lastX == -1) {
    lastX = x;
    lastY = y;
  }

  // Let's use black by setting RGB values to 0, and 255 alpha (completely opaque)
  r = 0;
  g = 0;
  b = 0;
  a = 255;

  // Select a fill style
  ctx.strokeStyle = "rgba(" + r + "," + g + "," + b + "," + (a / 255) + ")";

  // Set the line "cap" style to round, so lines at different angles can join into each other
  ctx.lineCap = "round";
  //ctx.lineJoin = "round";

  // Draw a filled line
  ctx.beginPath();

  // First, move to the old (previous) position
  ctx.moveTo(lastX, lastY);

  // Now draw a line to the current touch/pointer position
  ctx.lineTo(x, y);

  // Set the line thickness and draw the line
  ctx.lineWidth = size;
  ctx.stroke();

  ctx.closePath();

  // Update the last position to reference the current position
  lastX = x;
  lastY = y;
}

// Clear the canvas context using the canvas width and height
function clearCanvas(canvas, ctx) {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Keep track of the mouse button being pressed and draw a dot at current location
function sketchpad_mouseDown() {
  mouseDown = 1;
  drawLine(ctx, mouseX, mouseY, HANDWRITING_SIZE);
}

// Keep track of the mouse button being released
function sketchpad_mouseUp() {
  mouseDown = 0;

  // Reset lastX and lastY to -1 to indicate that they are now invalid, since we have lifted the "pen"
  lastX = -1;
  lastY = -1;
}

// Keep track of the mouse position and draw a dot if mouse button is currently pressed
function sketchpad_mouseMove(e) {
  // Update the mouse co-ordinates when moved
  getMousePos(e);

  // Draw a dot if the mouse button is currently being pressed
  if (mouseDown == 1) {
    drawLine(ctx, mouseX, mouseY, HANDWRITING_SIZE);
  }
}

// Get the current mouse position relative to the top-left of the canvas
function getMousePos(e) {
  if (!e)
    var e = event;

  if (e.offsetX) {
    mouseX = e.offsetX;
    mouseY = e.offsetY;
  } else if (e.layerX) {
    mouseX = e.layerX;
    mouseY = e.layerY;
  }
}

// Draw something when a touch start is detected
function sketchpad_touchStart() {
  // Update the touch co-ordinates
  getTouchPos();

  drawLine(ctx, touchX, touchY, HANDWRITING_SIZE);

  // Prevents an additional mousedown event being triggered
  event.preventDefault();
}

function sketchpad_touchEnd() {
  // Reset lastX and lastY to -1 to indicate that they are now invalid, since we have lifted the "pen"
  lastX = -1;
  lastY = -1;
}

// Draw something and prevent the default scrolling when touch movement is detected
function sketchpad_touchMove(e) {
  // Update the touch co-ordinates
  getTouchPos(e);

  // During a touchmove event, unlike a mousemove event, we don't need to check if the touch is engaged, since there will always be contact with the screen by definition.
  drawLine(ctx, touchX, touchY, HANDWRITING_SIZE);

  // Prevent a scrolling action as a result of this touchmove triggering.
  event.preventDefault();
}

// Get the touch position relative to the top-left of the canvas
// When we get the raw values of pageX and pageY below, they take into account the scrolling on the page
// but not the position relative to our target div. We'll adjust them using "target.offsetLeft" and
// "target.offsetTop" to get the correct values in relation to the top left of the canvas.
function getTouchPos(e) {
  if (!e)
    var e = event;

  if (e.touches) {
    if (e.touches.length == 1) { // Only deal with one finger
      var touch = e.touches[0]; // Get the information for finger #1
      touchX = touch.pageX - touch.target.offsetLeft;
      touchY = touch.pageY - touch.target.offsetTop;
    }
  }
}

// Set-up the canvas and add our event handlers after the page has loaded
function init() {
  // Get the specific canvas element from the HTML document
  canvas = document.getElementById('sketchpad');

  // If the browser supports the canvas tag, get the 2d drawing context for this canvas
  if (canvas.getContext)
    ctx = canvas.getContext('2d');

  // Check that we have a valid context to draw on/with before adding event handlers
  if (ctx) {
    // React to mouse events on the canvas, and mouseup on the entire document
    canvas.addEventListener('mousedown', sketchpad_mouseDown, false);
    canvas.addEventListener('mousemove', sketchpad_mouseMove, false);
    window.addEventListener('mouseup', sketchpad_mouseUp, false);

    // React to touch events on the canvas
    canvas.addEventListener('touchstart', sketchpad_touchStart, false);
    canvas.addEventListener('touchend', sketchpad_touchEnd, false);
    canvas.addEventListener('touchmove', sketchpad_touchMove, false);
  }
}

// Write some logic to initialize the text recognition
document.getElementById("img-to-txt").addEventListener("click", function () {
  // Disable button until the text recognition finishes
  let btn = this;
  btn.disable = true;

  Tesseract.create({
    workerPath: location.origin + "/js/worker.js",
    langPath: location.origin + "/lang/",
    corePath: location.origin + "/js/index.js",
  });

  // Tesseract.recognize("/img/image.png", "eng")
  // .progress((v) => {
  //   // v.status is a textual string of what Tesseract is doing
  //   // v.progress is a 0 - 1 decimal representation of the progress.
  //   // The progress resets for each new v.status,
  //   // but the major event is v.status == "recognizing text".
  //   console.log(v.status, status.progress);
  // })
  // .catch((err) => {
  //   console.error('OcrProvider: Failed to analyze text.', err);
  // })
  // .then((result) => {
  //   console.log(result);
  //   alert(result.text)
  // });


  Tesseract.recognize(canvas.toDataURL("image/png", 10), "spa")
    .progress((v) => {
      // v.status is a textual string of what Tesseract is doing
      // v.progress is a 0 - 1 decimal representation of the progress.
      // The progress resets for each new v.status,
      // but the major event is v.status == "recognizing text".
      console.log(v.status, status.progress);
    })
    .catch((err) => {
      console.error('OcrProvider: Failed to analyze text.', err);
    })
    .then((result) => {
      console.log(result);
      alert(result.text)
    });

  Tesseract.detect(canvas.toDataURL("image/png", 10))
    .progress((v) => {
      // v.status is a textual string of what Tesseract is doing
      // v.progress is a 0 - 1 decimal representation of the progress.
      // The progress resets for each new v.status,
      // but the major event is v.status == "recognizing text".
      console.log(v.status, status.progress);
    })
    .catch((err) => {
      console.error('OcrProvider: Failed to analyze text.', err);
    })
    .then((result) => {
      console.log(result);
      alert(result.text)
    });
}, false);


document.querySelector(".js-push-button").addEventListener("click", function () {
  var notification = new Notification("Polylines Notifications", {
    body: "You have a new message",
    icon: "favicon.ico",
    image: "favicon.ico",
    // actions: [{
    //     "action": "yes",
    //     "title": "Yes",
    //     "icon": "images/yes.png"
    //   },
    //   {
    //     "action": "no",
    //     "title": "No",
    //     "icon": "images/no.png"
    //   }
    // ]
  });
  notification.onclick = function (event) {
    event.preventDefault(); // prevent the browser from focusing the Notification's tab
    window.open(location.origin, "_blank");
  }
}, false);