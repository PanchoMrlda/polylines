<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta charset="utf-8" />
  <script async="async" type="text/javascript" src="/js/utils.js"></script>
  <script async="async" type="text/javascript" src="/js/devices.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
  <link href="/css/polylines.css" rel="stylesheet" />
  <title>Simple Polylines - Config</title>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="/favicon.ico" />
</head>

<body>
  <?php include 'views/layouts/nav.php'; ?>
  <section>
    <form action="/config" method="post">
      <section class="form-last-reading-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Tipo de vehículo</span>
        <span class="justify-content-center input-checkbox">
          <input class="input checkbox" type="radio" id="12v" name="vehicleType" value="12v" />
          <label for="12v">Vehículo 12V</label>
        </span>
        <span class="justify-content-center input-checkbox">
          <input class="input checkbox" type="radio" id="24v" name="vehicleType" value="24v" />
          <label for="24v">Vehículo 24V</label>
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend">
        <span class="justify-content-center span input-group-text">Defroster</span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Active" name="active" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDBl__Sp1C" />
          Set point turbina antivaho
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Nominalbatteryvoltage" name="nominalbatteryvoltage" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDBlCuRd1C" />
          Sensor corriente turbina antivaho
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Active" name="active" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDVa__Sp1C" />
          Set point grifo antivaho
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDVa__Po1C" />
          Posición real grifo antivaho
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDVa__Av1C" />
          Cosumo medio de corriente grifo antivaho (mA)
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDVa__St1C" />
          Estado real grifo antivaho
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDVa__Pk1C" />
          Cosumo máximo de corriente grifo antivaho (mA)
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDRe__Sp1C" />
          Set point motor Recirculación
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDRe__Po1C" />
          Posición real motor Recirculación
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDRe__Av1C" />
          Cosumo medio de corriente motor recirculación (mA)
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDRe__St1C" />
          Estado real motor Recirculación
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDRe__Pk1C" />
          Cosumo máximo de corriente motor recirculación (mA)
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDDi__Sp1C" />
          Set point motor Distribución de aire
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDDi__Po1C" />
          Posición real motor Distribución de aire
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDDi__Av1C" />
          Consumo medio de corriente motor Distribución de aire
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDDi__St1C" />
          Estado real motor Distribución de aire
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDDi__Pk1C" />
          Consumo máximo de corriente motor Distribución de aire (mA)
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDBlFiSt1C" />
          Sensor filtro en turbina de antivaho.
        </span>
      </section>
      <section class="form-last-reading-section input-group-prepend overflow-scroll">
        <span class="justify-content-center input-checkbox-large text-left">
          <input class="input-checkbox" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="text" placeholder="Node" name="nodeId" onchange="updateSection(this)" />
          <input class="input-checkbox-short" type="hidden" name="CDBlTeSo1C" />
          Sensor temperatura soplado turbina antivaho
        </span>
      </section>
      <button type="button" class="btn btn-primary" onclick="setConfig()">Set Config</button>
      <button type="button" class="btn btn-secondary" onclick="resetForm(this)">Reset Values</button>
    </form>
  </section>
</body>

<?php include 'views/layouts/spinner.php'; ?>

</html>