<?php

$hvc = fopen('config.hvc', 'w+');
foreach ($params as $section => $sectionParams) {
  unset($sectionParams['name']);
  // $sectionFilteredParams = array_diff();
  if (!empty(array_filter(array_values($sectionParams)))) {
    fwrite($hvc, "\n####################\n");
    fwrite($hvc, "\n[" . strtoupper($sectionParams['sectiontype']) . "]\n");
    unset($sectionParams['sectiontype']);
    fwrite($hvc, "name=$section\n");
    foreach ($sectionParams as $attribute => $value) {
      if (!empty($value)) {
        fwrite($hvc, "$attribute=$value\n");
      }
    }
  }
}
