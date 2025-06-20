<?php

declare(strict_types=1);

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR, [], new TestLogger);
	$command = new Commands\Copy;

	file_put_contents(TEMP_DIR . '/source-1.txt', '');
	file_put_contents(TEMP_DIR . '/source-2.txt', '');
	file_put_contents(TEMP_DIR . '/source-3.txt', '');

	$command->run($builder, ['from' => 'source-1.txt', 'to' => 'destination-1.txt']);
	$command->run($builder, [
		'files' => [
			'source-2.txt' => 'destination-2.txt',
			'source-3.txt' => 'destination-3.txt'
		]
	]);

	Assert::true(is_file(TEMP_DIR . '/destination-1.txt'));
	Assert::true(is_file(TEMP_DIR . '/destination-2.txt'));
	Assert::true(is_file(TEMP_DIR . '/destination-3.txt'));

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'from'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, ['from' => 'source.txt']);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'to'.");

});
