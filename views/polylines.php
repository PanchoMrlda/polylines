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
  <script src="js/c3.min.js"></script>
  <title>Simple Polylines</title>
  <style>
    /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
    #map {
      height: 100%;
    }

    /* Optional: Makes the sample page fill the window. */
    html,
    body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
  </style>
</head>

<body onload="initMap()">
  <section>
    <form action="/">
    <span>Device Name:</span>
      <select name="deviceId1" id="deviceId1Select" onchange="this.form.submit()">
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
      <select name="deviceId2" id="deviceId2Select" onchange="this.form.submit()">
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
    </form>
  </section>
  <div id="map"></div>
  <div id="tempChart"></div>
  <div id="pressureChart"></div>
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
    generateChart('#tempChart', [dates1, tempInt1, tempExt1]);
    generateChart('#pressureChart', [dates1, lowPressure1, highPressure1]);
  </script>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=
    <?php echo $secretsData['google']['mapsKey'] ?>
    &libraries=visualization">
  </script>
</body>

</html>