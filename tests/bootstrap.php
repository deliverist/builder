<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/libs/TestBuilder.php';

Tester\Environment::setup();

// TEMP DIR
@mkdir(__DIR__ . '/tmp');  # @ - adresář již může existovat
define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());
Tester\Helpers::purge(TEMP_DIR);


/**
 * @return void
 */
function test(callable $cb)
{
	$cb();
}
