<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$log = array();
	$builder = new Builder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = array($message, $type);
	};
	$command = new Commands\Remove;

	file_put_contents(TEMP_DIR . '/index.php', '');
	file_put_contents(TEMP_DIR . '/config.php', '');
	Assert::true(file_exists(TEMP_DIR . '/index.php'));
	Assert::true(file_exists(TEMP_DIR . '/config.php'));

	$command->run($builder, 'index.php');
	$command->run($builder, array(
		'config.php',
		'missing.txt',
	));
	Assert::false(file_exists(TEMP_DIR . '/index.php'));
	Assert::false(file_exists(TEMP_DIR . '/config.php'));

	Assert::same(array(
		array("Removing path 'index.php'.", Builder::INFO),
		array("Removing path 'config.php'.", Builder::INFO),
		array("Path 'missing.txt' not found.", Builder::WARNING),
	), $log);

});
