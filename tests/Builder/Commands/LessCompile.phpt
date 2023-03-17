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
	$command = new Commands\LessCompile;
	$inputPath = TEMP_DIR . '/www/components/styles.less';
	$outputPath = TEMP_DIR . '/www/components/styles.css';

	mkdir(dirname($inputPath), 0777, TRUE);
	file_put_contents($inputPath, '');
	file_put_contents($outputPath, ''); // lessc isnt run in real

	$command->run($builder, 'www/components/styles.less');
	$result = $builder->getRunnerResult();

	$command->setExecutable('/bin/lessc');
	$command->run($builder, 'www/components/styles.less');
	$result = $builder->getRunnerResult();

	Assert::same([
		["$ 'lessc' '-ru' '--clean-css' '--no-color' '$inputPath' '$outputPath'\n\nDirectory: \n\n=> 0\n\n", Builder::DEBUG],
		["$ '/bin/lessc' '-ru' '--clean-css' '--no-color' '$inputPath' '$outputPath'\n\nDirectory: \n\n=> 0\n\n", Builder::DEBUG],
	], $log);

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);

	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'file'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'not-found.less');

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.less' not found.");

});
