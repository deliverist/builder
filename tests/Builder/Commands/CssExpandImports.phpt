<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$log = array();
	$builder = new Builder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = array($message, $type);
	};
	$command = new Commands\CssExpandImports;

	file_put_contents(TEMP_DIR . '/styles.css', "@import 'http://example.com/';\n@import 'style2.css';\n@import 'dir/style3.css';\n");
	file_put_contents(TEMP_DIR . '/style2.css', "/* STYLE 2 */\n");
	mkdir(TEMP_DIR . '/dir');
	file_put_contents(TEMP_DIR . '/dir/style3.css', "/* STYLE 3 */\n@import '../style4.css';\n");
	file_put_contents(TEMP_DIR . '/style4.css', "/* STYLE 4 */\n");

	$command->run($builder, 'styles.css');

	Assert::same("@import 'http://example.com/';\n/* STYLE 2 */\n\n/* STYLE 3 */\n/* STYLE 4 */\n\n\n", file_get_contents(TEMP_DIR . '/styles.css'));

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);

	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'files'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'not-found.txt');

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.txt' not found.");

});
