<?php
require_once $_BASEDIR.'/core/base.php';
require_once $_BASEDIR.'/api/index.php';
require_once $_BASEDIR.'/core/mlam.php';
use \Core;
use \Core\MLAM;

$_MLAM = new MLAM();
$_MLAM->load_modules();
$_MLAM->exe_sess_events();
$_MLAM->call_event('core.onload');
$_MLAM->save_modules();