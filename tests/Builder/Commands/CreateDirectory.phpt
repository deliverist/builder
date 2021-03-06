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
	$command = new Commands\CreateDirectory;

	Assert::false(is_dir(TEMP_DIR . '/new-directory'));
	Assert::false(is_dir(TEMP_DIR . '/new-directory-2'));
	Assert::false(is_dir(TEMP_DIR . '/new-directory-3'));

	$command->run($builder, 'new-directory');
	$command->run($builder, array(
		'new-directory-2',
		'new-directory-3',
	));

	Assert::true(is_dir(TEMP_DIR . '/new-directory'));
	Assert::true(is_dir(TEMP_DIR . '/new-directory-2'));
	Assert::true(is_dir(TEMP_DIR . '/new-directory-3'));

	Assert::same(array(
		array("Create directory 'new-directory'.", Builder::INFO),
		array("Create directory 'new-directory-2'.", Builder::INFO),
		array("Create directory 'new-directory-3'.", Builder::INFO),
	), $log);

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);
	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'directories'.");

});
