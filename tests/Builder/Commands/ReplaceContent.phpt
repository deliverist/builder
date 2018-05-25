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
	$command = new Commands\ReplaceContent;

	$path = TEMP_DIR . '/test.txt';
	file_put_contents($path, "RewriteEngine on\n#production: RewriteRule XYZ\n");

	$command->run($builder, 'test.txt', array(
		'#production: ' => '',
	));

	Assert::same("RewriteEngine on\nRewriteRule XYZ\n", file_get_contents($path));

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);

	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'file'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'not-found.txt');

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.txt' not found.");

});
