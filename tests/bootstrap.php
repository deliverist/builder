<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();

// TEMP DIR
@mkdir(__DIR__ . '/tmp');  # @ - adresář již může existovat
define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());
Tester\Helpers::purge(TEMP_DIR);


function test($cb)
{
	$cb();
}
