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
<html lang="en">

<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
    <meta charset="utf-8"/>
    <script type="text/javascript" src="/js/mapStyles.js"></script>
    <script type="text/javascript" src="/js/utils.js"></script>
    <script async="async" type="text/javascript" src="/js/mapLogic.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <script src="/js/d3.v5.min.js"></script>
    <link href="/css/c3.css" rel="stylesheet"/>
    <link href="/css/polylines.css" rel="stylesheet"/>
    <script src="/js/c3.min.js"></script>
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
                <label class="justify-content-center span input-group-text">Previous reading</label>
                <label class="input">
                    <input class="input" type="text" value="<?php echo $dynamoDbData['deviceId1']['lastReading']; ?>"
                           name="from1" disabled="disabled"/>
                </label>
                <label class="input">
                    <input class="input" type="text" value="<?php echo $dynamoDbData['deviceId2']['lastReading']; ?>"
                           name="from2" disabled="disabled"/>
                </label>
            </section>
            <section class="form-date-section input-group-prepend">
                <label class="justify-content-center span input-group-text">Date</label>
                <label>
                    <input class="input" type="date" value="<?php echo date('Y-m-d', $from / 1000); ?>" name="from"
                           onchange="submitForm()"/>
                </label>

                <span class="justify-content-center input-checkbox">
            <label>
                <input class=" input checkbox" type="checkbox" name="lastHour" onchange="submitForm()"/>
            </label>
            Last Hour
        </span>
            </section>
            <section class="form-date-section input-group-prepend">
                <span class="justify-content-center span input-group-text">Data</span>
                <span class="justify-content-center input-checkbox">
            <label>
                <input class=" input checkbox" type="checkbox" name="pressureInBars" onchange="submitForm()"/>
            </label>
          Pressure in bars
        </span>
            </section>
            <section class="form-device-section input-group-prepend">
                <span class="justify-content-center span input-group-text">Device Name</span>
                <label for="deviceId1Select"></label>
                <input class="select input" list="devicesList1" id="deviceId1Select" name="deviceId1"
                       onchange="submitForm()">
                <datalist id="devicesList1">
                    <?php
                    printDevices($deviceData);
                    ?>
                </datalist>
                <label for="deviceId2Select"></label>
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
                <label for="distance2"></label>
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
<script async="async" defer="defer"
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_SESSION['secretsData']['google']['mapsKey'] ?>">
</script>
</body>

<?php include 'views/layouts/spinner.php'; ?>

</html>