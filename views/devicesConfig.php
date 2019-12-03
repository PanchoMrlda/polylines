<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <meta charset="utf-8" />
  <script async="async" type="text/javascript" src="/js/utils.js"></script>
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
  <section class="form-section-names input-group-prepend">
  <button type="button" class="btn btn-primary" name="createSection">Create Section</button>
    <select class="select input" name="sectionType" id="sectionTypeSelect" s>
      <option value="">-</option>
      <option value="ConnectionParams">Connection Params</option>
      <option value="Actuator">Actuator</option>
    </select>
    <input class="text-center" type="text" name="sectionName" placeholder="Section Name" />
    <input class="text-center" type="text" name="sectionDesc" placeholder="Section Description" />
    
  </section>
  <section class="table-responsive">
    <table class="table table-striped table-sm">
      <!-- <thead class="thead-dark">
        <tr>
          <th scope="col" section="nodes-section">Nodes</th>
          <th scope="col" section="defroster-section">Defroster</th>
          <th scope="col">Calefacción auxiliar 1</th>
          <th scope="col">Calefacción auxiliar 2</th>
          <th scope="col">Calefacción auxiliar 3</th>
          <th scope="col">Calefacción auxiliar 4</th>
          <th scope="col">Unidad techo</th>
          <th scope="col">Convectores</th>
        </tr>
      </thead> -->
      <tbody>
        <!-- <tr class="nodes-section">
          <th scope="row">Nodo</th>
          <td><input class="text-center" type="text" value="Controller Node" disabled="disabled" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Name" name="name" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="nodo" sectiontype="controller node" /></td>
        </tr>
        <tr class="nodes-section">
          <th scope="row">Nodo</th>
          <td><input class="text-center" type="text" value="Controller Node" disabled="disabled" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Name" name="name" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="nodo" sectiontype="controller node" /></td>
        </tr>
        <tr class="nodes-section">
          <th scope="row">Voltaje batería que consume el nodo</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Nominal Battery Voltage" name="nominalbatteryvoltage" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Battery Under Voltage Level" name="batteryundervoltagelevel" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Battery Over Voltage Level" name="batteryovervoltagelevel" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDNo__Vo1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Set point turbina antivaho</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Active" name="active" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Drive Mode" name="drive_mode" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDBl__Sp1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Sensor corriente turbina antivaho</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Nominalbatteryvoltage" name="nominalbatteryvoltage" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDBlCuRd1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Set point grifo antivaho</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Active" name="active" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Type" name="type" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="text" placeholder="Function" name="function" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDVa__Sp1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Posición real grifo antivaho</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDVa__Po1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Cosumo medio de corriente grifo antivaho (mA)</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDVa__Av1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Estado real grifo antivaho</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDVa__St1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Cosumo máximo de corriente grifo antivaho (mA)</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDVa__Pk1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Set point motor Recirculación</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDRe__Sp1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Posición real motor Recirculación</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDRe__Po1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Cosumo medio de corriente motor recirculación (mA)</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDRe__Av1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Estado real motor Recirculación</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDRe__St1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Cosumo máximo de corriente motor recirculación (mA)</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDRe__Pk1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Set point motor Distribución de aire</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDDi__Sp1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Posición real motor Distribución de aire</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDDi__Po1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Consumo medio de corriente motor Distribución de aire</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDDi__Av1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Estado real motor Distribución de aire</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDDi__St1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Consumo máximo de corriente motor Distribución de aire (mA)</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDDi__Pk1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Sensor filtro en turbina de antivaho.</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDBlFiSt1C" /></td>
        </tr>
        <tr class="defroster-section">
          <th scope="row">Sensor temperatura soplado turbina antivaho</th>
          <td><input class="text-center" type="text" placeholder="Section Type" name="sectionType" onchange="updateSection(this)" /></td>
          <td class="text-center"><input class="text-center" type="text" placeholder="Node Id" name="nodeId" onchange="updateSection(this)" /></td>
          <td><input class="text-center" type="hidden" name="CDBlTeSo1C" /></td>
        </tr> -->
      </tbody>
    </table>
  </section>
  <button type="button" class="btn btn-primary" onclick="setConfig()">Set Config</button>
  <button type="button" class="btn btn-secondary" onclick="resetForm(this)">Reset Values</button>
  <script async="async" type="text/javascript" src="/js/devices.js"></script>
</body>

<?php include 'views/layouts/spinner.php'; ?>

</html>