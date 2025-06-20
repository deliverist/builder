<?php

declare(strict_types=1);

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$logger = new TestLogger;
	$builder = new Builder(TEMP_DIR, [], $logger);
	$command = new Commands\Remove;

	file_put_contents(TEMP_DIR . '/index.php', '');
	file_put_contents(TEMP_DIR . '/config.php', '');
	Assert::true(file_exists(TEMP_DIR . '/index.php'));
	Assert::true(file_exists(TEMP_DIR . '/config.php'));

	$command->run($builder, ['path' => 'index.php']);
	$command->run($builder, [
		'paths' => [
			'config.php',
			'missing.txt',
		],
	]);
	Assert::false(file_exists(TEMP_DIR . '/index.php'));
	Assert::false(file_exists(TEMP_DIR . '/config.php'));

	Assert::same([
		"[INFO] Removing path 'index.php'.",
		"[INFO] Removing path 'config.php'.",
		"[WARNING] Path 'missing.txt' not found.",
	], $logger->getLog());

});
