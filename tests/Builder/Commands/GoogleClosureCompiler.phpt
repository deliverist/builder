<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../libs/TestBuilder.php';


test(function () {

	$log = [];
	$builder = new TestBuilder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = [$message, $type];
	};
	$command = new Commands\GoogleClosureCompiler;

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);

	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'files'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'not-found.js');

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.js' not found.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, [
			'not-found-1.js',
			'not-found-2.js',
		]);

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found-1.js' not found.");

});
