<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <script type="text/javascript" src="js/mapStyles.js"></script>
  <script async type="text/javascript" src="js/mapLogic.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <script src="https://d3js.org/d3.v5.min.js"></script>
  <link href="css/c3.css" rel="stylesheet">
  <link href="css/polylines.css" rel="stylesheet">
  <script src="js/c3.min.js"></script>
  <title>Simple Polylines</title>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body onload="initMap()">
  <section>
    <form action="/">
      <section class="form-last-reading-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Previous reading</span>
        <input class="input" type="text" value="<?php echo $lastReading1; ?>" name="from" onchange="this.form.submit()" disabled>
        <input class="input" type="text" value="<?php echo $lastReading2; ?>" name="from" onchange="this.form.submit()" disabled>
      </section>
      <section class="form-date-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Date</span>
        <input class="input" type="date" value="<?php echo date('Y-m-d', $from / 1000); ?>" name="from" onchange="this.form.submit()">
      </section>
      <section class="form-device-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Device Name</span>
        <select class="select input" name="deviceId1" id="deviceId1Select" onchange="this.form.submit()">
          <option value="">-</option>
          <?php
          foreach ($deviceNames as $deviceZone => $deviceList) {
            echo "<optgroup label='$deviceZone'>";
            foreach ($deviceList as $deviceName) {
              echo "<option value='$deviceName'>$deviceName</option>";
            }
            echo '</optgroup>';
          }
          ?>
        </select>
        <select class="select input" name="deviceId2" id="deviceId2Select" onchange="this.form.submit()">
          <option value="">-</option>
          <?php
          foreach ($deviceNames as $deviceZone => $deviceList) {
            echo "<optgroup label='$deviceZone'>";
            foreach ($deviceList as $deviceName) {
              echo "<option value='$deviceName'>$deviceName</option>";
            }
            echo '</optgroup>';
          }
          ?>
        </select>
      </section>
      <section class="form-distance-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Travelled distance</span>
        <input class="input" type="text" value="" name="distance1" id="distance1" disabled>
        <input class="input" type="text" value="" name="distance2" id="distance2" disabled>
      </section>
    </form>
  </section>
  <section class="map-container">
    <div id="map" class="chart"></div>
  </section>
  <div id="tempChart" class="chart"></div>
  <div id="pressureChart" class="chart"></div>
  <div id="voltageChart" class="chart"></div>
  <script>
    var dates1 = <?php echo (json_encode($dates1)) ?>;
    dates1.unshift('times');
    var locations1 = <?php echo (json_encode($locations1)) ?>;
    var locations2 = <?php echo (json_encode($locations2)) ?>;
    var tempInt1 = <?php echo (json_encode($tempInt1)) ?>;
    tempInt1.unshift('Temp Int');
    var tempExt1 = <?php echo (json_encode($tempExt1)) ?>;
    tempExt1.unshift('Temp Ext');
    var highPressure1 = <?php echo (json_encode($highPressure1)) ?>;
    highPressure1.unshift('High Pressure');
    var lowPressure1 = <?php echo (json_encode($lowPressure1)) ?>;
    lowPressure1.unshift('Low Pressure');
    var compressor1 = <?php echo (json_encode($compressor1)) ?>;
    compressor1.unshift('Compressor');
    var blower1 = <?php echo (json_encode($blower1)) ?>;
    blower1.unshift('Blower');
  </script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=
    <?php echo $secretsData['google']['mapsKey'] ?>
    &libraries=visualization">
  </script>
</body>

<div class="spinner-border text-primary" role="status">
  <span class="sr-only">Loading...</span>
</div>

</html>