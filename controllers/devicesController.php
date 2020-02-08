<?php

$hvc = fopen('config.hvc', 'w+');
foreach ($params as $section => $sectionParams) {
  $comments = array_key_exists('comments', $sectionParams) ? $sectionParams['comments'] : '';
  unset($sectionParams['comments']);
  unset($sectionParams['name']);
  $type = str_replace(' ', '_', strtoupper($sectionParams['sectiontype']));
  unset($sectionParams['sectiontype']);
  if (!empty(array_filter(array_values($sectionParams)))) {
    fwrite($hvc, "\n####################\n");
    if (!empty($comments)) {
      fwrite($hvc, "\n# $comments\n");
    }
    fwrite($hvc, "\n[$type]\n");
    if (!empty($section) && $section != "Connection Params") {
      fwrite($hvc, "name=$section\n");
    }
    foreach ($sectionParams as $attribute => $value) {
      if ($value != null) {
        fwrite($hvc, "$attribute=$value\n");
      }
    }
  }
}
