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
	$command = new Commands\CreateDirectory;

	Assert::false(is_dir(TEMP_DIR . '/new-directory'));
	Assert::false(is_dir(TEMP_DIR . '/new-directory-2'));
	Assert::false(is_dir(TEMP_DIR . '/new-directory-3'));

	$command->run($builder, ['directory' => 'new-directory']);
	$command->run($builder, [
		'directories' => [
			'new-directory-2',
			'new-directory-3',
		]
	]);

	Assert::true(is_dir(TEMP_DIR . '/new-directory'));
	Assert::true(is_dir(TEMP_DIR . '/new-directory-2'));
	Assert::true(is_dir(TEMP_DIR . '/new-directory-3'));

	Assert::same([
		"[INFO] Create directory 'new-directory'.",
		"[INFO] Create directory 'new-directory-2'.",
		"[INFO] Create directory 'new-directory-3'.",
	], $logger->getLog());

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);
	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'directory'.");

});
