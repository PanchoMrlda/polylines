<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
  <meta charset="utf-8">
  <script type="text/javascript" src="js/mapStyles.js"></script>
  <script async defer type="text/javascript" src="js/mapLogic.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
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

<body>
  <section>
    <form action="/">
    <span>Device Name:</span>
      <select name="deviceId1" id="deviceId1Select" onchange="this.form.submit()">
        <option value="">-</option>
        <?php
        foreach ($deviceNames as $deviceName) {
          echo "<option value='$deviceName'>$deviceName</option>";
        }
        ?>
      </select>
      <select name="deviceId2" id="deviceId2Select" onchange="this.form.submit()">
        <option value="">-</option>
        <?php
        foreach ($deviceNames as $deviceName) {
          echo "<option value='$deviceName'>$deviceName</option>";
        }
        ?>
      </select>
    </form>
  </section>
  <div id="map"></div>
  <script>
    var locations1 = [<?php
                      if (count($payloads1) != 0) {
                        foreach ($payloads1 as $values) {
                          echo '{lat:' . $values['g']['la'] . ', lng:' . $values['g']['lo'] . '},';
                        }
                      } else {
                        echo '{lat: 40.41695, lng: -3.70321}';
                      }
                      ?>];
    var locations2 = [<?php
                      if (count($payloads2) != 0) {
                        foreach ($payloads2 as $values) {
                          echo '{lat:' . $values['g']['la'] . ', lng:' . $values['g']['lo'] . '},';
                        }
                      } else {
                        echo '{lat: 40.41695, lng: -3.70321}';
                      }
                      ?>];
  </script>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=
    <?php echo $secretsData['google']['mapsKey'] ?>
    &libraries=visualization&callback=initMap">
  </script>
</body>

</html>