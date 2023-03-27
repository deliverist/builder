<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR, [], new TestLogger);
	$command = new Commands\ApacheImports;

	file_put_contents(TEMP_DIR . '/imports.txt', "<!--#include file=\"import-a.txt\" --><!--#include file=\"import-b.txt\" -->\n");
	file_put_contents(TEMP_DIR . '/import-a.txt', "IMPORT A\n<!--#include file=\"import-a-a.txt\" -->\n");
	file_put_contents(TEMP_DIR . '/import-a-a.txt', "IMPORT A-A\n");
	file_put_contents(TEMP_DIR . '/import-b.txt', "IMPORT B\n");

	$command->run($builder, [
		'file' => 'imports.txt'
	]);

	Assert::same("IMPORT A\nIMPORT A-A\n\nIMPORT B\n\n", file_get_contents(TEMP_DIR . '/imports.txt'));
	Assert::false(is_file(TEMP_DIR . '/import-a.txt'));
	Assert::false(is_file(TEMP_DIR . '/import-a-a.txt'));
	Assert::false(is_file(TEMP_DIR . '/import-b.txt'));

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'file'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, ['file' => 'not-found.txt']);

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.txt' not found.");

});
