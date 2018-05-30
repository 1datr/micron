<?php
require_once '/core/base.php';
require_once '/api/index.php';
require_once '/core/mlam.php';
use \Core;

$_MLAM = new MLAM();
$_MLAM->load_modules();
$_MLAM->call_event('core.onload');