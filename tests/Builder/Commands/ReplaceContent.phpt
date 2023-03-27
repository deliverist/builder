<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR, [], new TestLogger);
	$command = new Commands\ReplaceContent;

	$path = TEMP_DIR . '/test.txt';
	file_put_contents($path, "RewriteEngine on\n#production: RewriteRule XYZ\n");

	$command->run($builder, [
		'file' => 'test.txt',
		'replacements' => [
			'#production: ' => '',
		]
	]);

	Assert::same("RewriteEngine on\nRewriteRule XYZ\n", file_get_contents($path));

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, ['replacements' => []]);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'file'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, ['file' => 'not-found.txt', 'replacements' => []]);

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.txt' not found.");

});
