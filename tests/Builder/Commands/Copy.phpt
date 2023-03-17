<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$log = [];
	$builder = new Builder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = [$message, $type];
	};
	$command = new Commands\Copy;

	file_put_contents(TEMP_DIR . '/source-1.txt', '');
	file_put_contents(TEMP_DIR . '/source-2.txt', '');
	file_put_contents(TEMP_DIR . '/source-3.txt', '');

	$command->run($builder, 'source-1.txt', 'destination-1.txt');
	$command->run($builder, [
		'source-2.txt' => 'destination-2.txt',
		'source-3.txt' => 'destination-3.txt'
	]);

	Assert::true(is_file(TEMP_DIR . '/destination-1.txt'));
	Assert::true(is_file(TEMP_DIR . '/destination-2.txt'));
	Assert::true(is_file(TEMP_DIR . '/destination-3.txt'));

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);

	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'source'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'source.txt');

	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'destination'.");

});
