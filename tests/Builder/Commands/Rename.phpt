<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR);
	$command = new Commands\Rename;

	file_put_contents(TEMP_DIR . '/index.php', '');
	Assert::true(file_exists(TEMP_DIR . '/index.php'));
	Assert::false(file_exists(TEMP_DIR . '/www/index.php'));

	$command->run($builder, 'index.php', 'www/index.php');
	Assert::false(file_exists(TEMP_DIR . '/index.php'));
	Assert::true(file_exists(TEMP_DIR . '/www/index.php'));

});


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR);
	$command = new Commands\Rename;

	file_put_contents(TEMP_DIR . '/index.php', '');
	file_put_contents(TEMP_DIR . '/config.php', '');
	Assert::true(file_exists(TEMP_DIR . '/index.php'));
	Assert::true(file_exists(TEMP_DIR . '/config.php'));
	Assert::false(file_exists(TEMP_DIR . '/www/index.php'));
	Assert::false(file_exists(TEMP_DIR . '/app/config.php'));

	$command->run($builder, [
		'index.php' => 'www/index.php',
		'config.php' => 'app/config.php',
	]);
	Assert::false(file_exists(TEMP_DIR . '/index.php'));
	Assert::false(file_exists(TEMP_DIR . '/config.php'));
	Assert::true(file_exists(TEMP_DIR . '/www/index.php'));
	Assert::true(file_exists(TEMP_DIR . '/app/config.php'));

});


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR);
	$command = new Commands\Rename;

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);
	}, 'Deliverist\Builder\Commands\RenameException', "Missing parameter 'from'.");


	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'file.txt');
	}, 'Deliverist\Builder\Commands\RenameException', "Missing parameter 'to'.");


	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'missing.txt', 'renamed.txt');
	}, 'Deliverist\Builder\Commands\RenameException', "Renaming of 'missing.txt' to 'renamed.txt' failed.");

});
