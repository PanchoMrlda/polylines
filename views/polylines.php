<?php
$deviceData = json_decode(file_get_contents('secrets.json'), true)['aws']['deviceNames'];
function printDevices($deviceData)
{
  foreach ($deviceData as $array) {
    foreach ($array as $companyName => $devicesArray) {
      echo "<optgroup label='$companyName'>";
      foreach ($devicesArray as $devicesInfo) {
        foreach ($devicesInfo as $vehicleId => $deviceNames) {
          echo "<optgroup label='" . '&nbsp;&nbsp;&nbsp;&nbsp;' . "$vehicleId'>";
          foreach ($deviceNames as $deviceName) {
            echo "<option value='$deviceName'>&nbsp;&nbsp;&nbsp;&nbsp;$deviceName</option>";
          }
          echo '</optgroup>';
        }
      }
      echo '</optgroup>';
    }
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta charset="utf-8" />
  <script type="text/javascript" src="js/mapStyles.js"></script>
  <script type="text/javascript" src="js/utils.js"></script>
  <script async="async" type="text/javascript" src="js/mapLogic.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
  <script src="js/d3.v5.min.js"></script>
  <link href="css/c3.css" rel="stylesheet" />
  <link href="css/polylines.css" rel="stylesheet" />
  <script src="js/c3.min.js"></script>
  <title>Simple Polylines - Map</title>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="/favicon.ico" />
</head>

<body onload="initMap()">
  <?php include 'views/layouts/nav.php'; ?>
  <section>
    <form class="map-form" action="/">
      <section class="form-last-reading-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Previous reading</span>
        <input class="input" type="text" value="<?php echo $lastReading1; ?>" name="from1" disabled="disabled" />
        <input class="input" type="text" value="<?php echo $lastReading2; ?>" name="from2" disabled="disabled" />
      </section>
      <section class="form-date-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Date</span>
        <input class="input" type="date" value="<?php echo date('Y-m-d', $from / 1000); ?>" name="from" onchange="submitForm()" />
        <span class="justify-content-center input-checkbox">
          <input class=" input checkbox" type="checkbox" value="true" name="lastHour" onchange="submitForm()" />
          Last Hour
        </span>
      </section>
      <section class="form-device-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Device Name</span>
        <select class="select input" name="deviceId1" id="deviceId1Select" onchange="submitForm()">
          <option value="">-</option>
          <?php
          printDevices($deviceData);
          ?>
        </select>
        <select class="select input" name="deviceId2" id="deviceId2Select" onchange="submitForm()">
          <option value="">-</option>
          <?php
          printDevices($deviceData);
          ?>
        </select>
      </section>
      <section class="form-distance-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Travelled distance</span>
        <input class="input" type="text" value="" name="distance1" id="distance1" disabled="disabled" />
        <input class="input" type="text" value="" name="distance2" id="distance2" disabled="disabled" />
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
    // Load profile
    var profile = <?php echo (json_encode($_SESSION['profile'])) ?>;
    // Variables for bus 1
    var dates1 = <?php echo (json_encode($dates1)) ?>;
    dates1.unshift('times');
    var locations1 = <?php echo (json_encode($locations1)) ?>;
    var tempInt1 = <?php echo (json_encode($tempInt1)) ?>;
    tempInt1.unshift('Temp Int' + ' (<?php echo $deviceId1 ?>)');
    var tempExt1 = <?php echo (json_encode($tempExt1)) ?>;
    tempExt1.unshift('Temp Ext' + ' (<?php echo $deviceId1 ?>)');
    var highPressure1 = <?php echo (json_encode($highPressure1)) ?>;
    highPressure1.unshift('High Pressure' + ' (<?php echo $deviceId1 ?>)');
    var lowPressure1 = <?php echo (json_encode($lowPressure1)) ?>;
    lowPressure1.unshift('Low Pressure' + ' (<?php echo $deviceId1 ?>)');
    // var compressor1 = <?php // echo (json_encode($compressor1)) ?>;
    // compressor1.unshift('Compressor' + ' (<?php echo $deviceId1 ?>)');
    // var blower1 = <?php // echo (json_encode($blower1)) ?>;
    // blower1.unshift('Blower' + ' (<?php echo $deviceId1 ?>)');
    // Variables bus 2
    var dates2 = <?php echo (json_encode($dates2)) ?>;
    dates2.unshift('times');
    var locations2 = <?php echo (json_encode($locations2)) ?>;
    var tempInt2 = <?php echo (json_encode($tempInt2)) ?>;
    tempInt2.unshift('Temp Int' + ' (<?php echo $deviceId2 ?>)');
    var tempExt2 = <?php echo (json_encode($tempExt2)) ?>;
    tempExt2.unshift('Temp Ext' + ' (<?php echo $deviceId2 ?>)');
    var highPressure2 = <?php echo (json_encode($highPressure2)) ?>;
    highPressure2.unshift('High Pressure' + ' (<?php echo $deviceId2 ?>)');
    var lowPressure2 = <?php echo (json_encode($lowPressure2)) ?>;
    lowPressure2.unshift('Low Pressure' + ' (<?php echo $deviceId2 ?>)');
    // var compressor2 = <?php // echo (json_encode($compressor2)) ?>;
    // compressor2.unshift('Compressor' + ' (<?php echo $deviceId2 ?>)');
    // var blower2 = <?php // echo (json_encode($blower2)) ?>;
    // blower2.unshift('Blower' + ' (<?php echo $deviceId2 ?>)');
  </script>
  <script async="async" defer="defer" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_SESSION['secretsData']['google']['mapsKey'] ?>">
  </script>
</body>

<?php include 'views/layouts/spinner.php'; ?>

</html>