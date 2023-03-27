<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	$logger = new TestLogger;
	$builder = new TestBuilder(TEMP_DIR, [], $logger);
	$command = new Commands\LessCompile;
	$inputPath = TEMP_DIR . '/www/components/styles.less';
	$outputPath = TEMP_DIR . '/www/components/styles.css';

	mkdir(dirname($inputPath), 0777, TRUE);
	file_put_contents($inputPath, '');
	file_put_contents($outputPath, ''); // lessc isnt run in real

	$command->run($builder, ['file' => 'www/components/styles.less']);
	$result = $builder->getRunnerResult();

	$command->setExecutable('/bin/lessc');
	$command->run($builder, ['file' => 'www/components/styles.less']);
	$result = $builder->getRunnerResult();

	Assert::same([
		"[DEBUG] $ 'lessc' '-ru' '--clean-css' '--no-color' '$inputPath' '$outputPath'\n\nDirectory: \n\n=> 0\n\n",
		"[DEBUG] $ '/bin/lessc' '-ru' '--clean-css' '--no-color' '$inputPath' '$outputPath'\n\nDirectory: \n\n=> 0\n\n",
	], $logger->getLog());

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'file'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, ['file' => 'not-found.less']);

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.less' not found.");

});
