<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


class FileContent
{
	private $content;


	private function __construct($content)
	{
		$this->content = $content;
	}


	public function toString()
	{
		return $this->content;
	}


	public function write($path)
	{
		@mkdir(dirname($path), 0777, TRUE);
		file_put_contents($path, $this->content);
	}


	public static function create(array $lines)
	{
		return new self(implode("\n", $lines) . "\n");
	}
}


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$log = [];
	$builder = new Builder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = [$message, $type];
	};
	$command = new Commands\CssExpandImports;

	FileContent::create([
		"@import 'http://example.com/';",
		"@import 'style2.css';",
		"@import 'dir/style3.css';",
		"@import 'style5.css' all;",
		"@import 'style6.css' screen and (min-width: 50em);",
		"@import 'style7.css' screen and (min-width: 50em);",
		"@import 'style10.css' screen and (min-width: 300px);"
	])->write(TEMP_DIR . '/styles.css');

	FileContent::create([
		"/* STYLE 2 */",
	])->write(TEMP_DIR . '/style2.css');

	FileContent::create([
		"/* STYLE 3 */",
		"@import '../style4.css';",
	])->write(TEMP_DIR . '/dir/style3.css');

	FileContent::create([
		"/* STYLE 4 */",
	])->write(TEMP_DIR . '/style4.css');

	FileContent::create([
		"/* STYLE 5 */",
	])->write(TEMP_DIR . '/style5.css');

	FileContent::create([
		"/* STYLE 6 */",
	])->write(TEMP_DIR . '/style6.css');

	FileContent::create([
		"/* STYLE 7 */",
		"@import 'style8.css'",
	])->write(TEMP_DIR . '/style7.css');

	FileContent::create([
		"/* STYLE 8 */",
		"@import 'style9.css' screen",
	])->write(TEMP_DIR . '/style8.css');

	FileContent::create([
		"/* STYLE 9 */",
	])->write(TEMP_DIR . '/style9.css');

	FileContent::create([
		"/* STYLE 10 */",
		"@import 'style11.css' screen and (min-width: 300px);",
	])->write(TEMP_DIR . '/style10.css');

	FileContent::create([
		"/* STYLE 11 */",
	])->write(TEMP_DIR . '/style11.css');

	$command->run($builder, 'styles.css');

	Assert::same(
		FileContent::create([
			"@import 'http://example.com/';",
			"/* STYLE 2 */",
			"/* STYLE 3 */",
			"/* STYLE 4 */",
			"/* STYLE 5 */",
			"@media screen and (min-width: 50em) {",
			"/* STYLE 6 */",
			"}",
			"@media screen and (min-width: 50em) {",
			"/* STYLE 7 */",
			"@import 'style8.css'",
			"}",
			"@media screen and (min-width: 300px) {",
			"/* STYLE 10 */",
			"/* STYLE 11 */",
			"}",
		])->toString(),
		file_get_contents(TEMP_DIR . '/styles.css')
	);

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);

	}, 'Deliverist\Builder\InvalidArgumentException', "Missing parameter 'files'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'not-found.txt');

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.txt' not found.");

});
