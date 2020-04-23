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
                        echo "<option value='$deviceName'>&nbsp;&nbsp;&nbsp;&nbsp;$vehicleId</option>";
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
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8"/>
    <script type="text/javascript" src="js/mapStyles.js"></script>
    <script type="text/javascript" src="js/utils.js"></script>
    <script async="async" type="text/javascript" src="js/mapLogic.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <script src="js/d3.v5.min.js"></script>
    <link href="css/c3.css" rel="stylesheet"/>
    <link href="css/polylines.css" rel="stylesheet"/>
    <script src="js/c3.min.js"></script>
    <title>Simple Polylines - Map</title>
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
</head>

<body onload="initMap()">
<?php include 'views/layouts/nav.php'; ?>
<section class="options-container">
    <section class="p-2 flex-fill form-container">
        <form class="map-form" action="/">
            <section class="form-last-reading-section input-group-prepend">
                <span class="justify-content-center span input-group-text">Previous reading</span>
                <input class="input" type="text" value="<?php echo $lastReading1; ?>" name="from1" disabled="disabled"/>
                <input class="input" type="text" value="<?php echo $lastReading2; ?>" name="from2" disabled="disabled"/>
            </section>
            <section class="form-date-section input-group-prepend">
                <span class="justify-content-center span input-group-text">Date</span>
                <input class="input" type="date" value="<?php echo date('Y-m-d', $from / 1000); ?>" name="from"
                       onchange="submitForm()"/>
                <span class="justify-content-center input-checkbox">
          <input class=" input checkbox" type="checkbox" name="lastHour" onchange="submitForm()"/>
          Last Hour
        </span>
            </section>
            <section class="form-date-section input-group-prepend">
                <span class="justify-content-center span input-group-text">Data</span>
                <span class="justify-content-center input-checkbox">
          <input class=" input checkbox" type="checkbox" name="pressureInBars" onchange="submitForm()"/>
          Pressure in bars
        </span>
            </section>
            <section class="form-device-section input-group-prepend">
                <span class="justify-content-center span input-group-text">Device Name</span>
                <input class="select input" list="devicesList1" id="deviceId1Select" name="deviceId1"
                       onchange="submitForm()">
                <datalist id="devicesList1">
                    <?php
                    printDevices($deviceData);
                    ?>
                </datalist>
                <input class="select input" list="devicesList2" id="deviceId2Select" name="deviceId2"
                       onchange="submitForm()">
                <datalist id="devicesList2">
                    <?php
                    printDevices($deviceData);
                    ?>
                </datalist>
            </section>
            <section class="form-distance-section input-group-prepend">
                <label class="justify-content-center span input-group-text" for="distance1">Travelled distance</label>
                <input class="input" type="text" value="" name="distance1" id="distance1" disabled="disabled"/>
                <input class="input" type="text" value="" name="distance2" id="distance2" disabled="disabled"/>
            </section>
        </form>
    </section>
    <section class="p-2 flex-fill map-container">
        <div id="map" class="chart"></div>
    </section>
</section>
<div id="tempChart" class="chart"></div>
<div id="pressureChart" class="chart"></div>
<div id="voltageChart" class="chart"></div>
<script>
    // Load profile
    let profile = <?php echo(json_encode($_SESSION['profile'])) ?>;
    // Variables for bus 1
    let dates1 = <?php echo(json_encode($dates1)) ?>;
    dates1.unshift('times');
    let locations1 = <?php echo(json_encode($locations1)) ?>;
    let tempInt1 = <?php echo(json_encode($tempInt1)) ?>;
    tempInt1.unshift('Temp Int' + ' (<?php echo $deviceId1 ?>)');
    let tempExt1 = <?php echo(json_encode($tempExt1)) ?>;
    tempExt1.unshift('Temp Ext' + ' (<?php echo $deviceId1 ?>)');
    let highPressure1 = <?php echo(json_encode($highPressure1)) ?>;
    highPressure1.unshift('High Pressure' + ' (<?php echo $deviceId1 ?>)');
    let lowPressure1 = <?php echo(json_encode($lowPressure1)) ?>;
    lowPressure1.unshift('Low Pressure' + ' (<?php echo $deviceId1 ?>)');
    // let compressor1 = <?php // echo (json_encode($compressor1)) ?>;
    // compressor1.unshift('Compressor' + ' (<?php echo $deviceId1 ?>)');
    // let blower1 = <?php // echo (json_encode($blower1)) ?>;
    // blower1.unshift('Blower' + ' (<?php echo $deviceId1 ?>)');
    // Variables bus 2
    let dates2 = <?php echo(json_encode($dates2)) ?>;
    dates2.unshift('times');
    let locations2 = <?php echo(json_encode($locations2)) ?>;
    let tempInt2 = <?php echo(json_encode($tempInt2)) ?>;
    tempInt2.unshift('Temp Int' + ' (<?php echo $deviceId2 ?>)');
    let tempExt2 = <?php echo(json_encode($tempExt2)) ?>;
    tempExt2.unshift('Temp Ext' + ' (<?php echo $deviceId2 ?>)');
    let highPressure2 = <?php echo(json_encode($highPressure2)) ?>;
    highPressure2.unshift('High Pressure' + ' (<?php echo $deviceId2 ?>)');
    let lowPressure2 = <?php echo(json_encode($lowPressure2)) ?>;
    lowPressure2.unshift('Low Pressure' + ' (<?php echo $deviceId2 ?>)');
    // let compressor2 = <?php // echo (json_encode($compressor2)) ?>;
    // compressor2.unshift('Compressor' + ' (<?php echo $deviceId2 ?>)');
    // let blower2 = <?php // echo (json_encode($blower2)) ?>;
    // blower2.unshift('Blower' + ' (<?php echo $deviceId2 ?>)');
</script>
<script async="async" defer="defer"
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_SESSION['secretsData']['google']['mapsKey'] ?>">
</script>
</body>

<?php include 'views/layouts/spinner.php'; ?>

</html>