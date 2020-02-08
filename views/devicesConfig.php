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
    <select class="select input" name="sectionType" id="sectionTypeSelect">
      <option value="">-</option>
      <option value="Controller Node">Controller Node</option>
      <option value="Connection Params">Connection Params</option>
      <option value="Actuator">Actuator</option>
      <option value="Blower">Blower</option>
      <option value="Voltage Mon">Voltage Mon</option>
    </select>
    <input class="text-center" type="text" name="sectionName" placeholder="Section Name" />
    <input class="text-center" type="text" name="sectionDesc" placeholder="Section Description" />
  </section>
  <section class="table-responsive">
    <table class="table table-striped table-sm">
      <tbody>
        <tr>
          <th>Comments</th>
          <td colspan="4">
            <input class="text-center" name="name" placeholder="General Comments" type="text" style="min-width:100%;" onchange="updateSection(this)" data-toggle="tooltip" data-placement="bottom" title="Comentarios a modo de explicación del uso dado al fichero. Por ejemplo, para qué mando se ha creado (Newton, Einstein), para qué vehículo (furgón, autocar, doble piso, etc)">
          </td>
          <td>
            <input class="text-center" type="hidden" name="" sectiontype="General Comments">
          </td>
        </tr>
      </tbody>
    </table>
  </section>
  <button type="button" class="btn btn-primary" name="setConfig" onclick="setConfig()">Set Config</button>
  <button type="button" class="btn btn-secondary" onclick="resetForm(this)">Reset Values</button>
  <script async="async" type="text/javascript" src="/js/devices.js"></script>
</body>

<?php include 'views/layouts/spinner.php'; ?>

</html>