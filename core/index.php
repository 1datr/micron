<?php
require_once '/core/base.php';
require_once '/api/index.php';
require_once '/core/mlam.php';
use \Core;



$mod_exec = new MLAM();
$mod_exec->load_modules();
$mod_exec->call_event('core.onload');