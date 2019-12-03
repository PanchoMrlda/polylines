<?php

$hvc = fopen('config.hvc', 'w+');
foreach ($params as $section => $sectionParams) {
  unset($sectionParams['name']);
  if (!empty(array_filter(array_values($sectionParams)))) {
    $type = str_replace(' ', '_', strtoupper($sectionParams['sectiontype']));
    unset($sectionParams['sectiontype']);
    fwrite($hvc, "\n####################\n");
    fwrite($hvc, "\n[$type]\n");
    if (!empty($section)) {
      fwrite($hvc, "name=$section\n");
    }
    foreach ($sectionParams as $attribute => $value) {
      if ($value != null) {
        fwrite($hvc, "$attribute=$value\n");
      }
    }
  }
}
