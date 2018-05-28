<?php
require_once '/core/base.php';
require_once '/api/index.php';
require_once '/core/moduleexec.php';
use \Core;



$mod_exec = new ModuleExecuter();
$mod_exec->load_modules();
$mod_exec->call_event('core.onload');