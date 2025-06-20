<?php

declare(strict_types=1);

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR, [], new TestLogger);
	$command = new Commands\MinifyContent;

	$path = TEMP_DIR . '/test.txt';
	file_put_contents($path, "{block content}
			<h1>Lorem ipsum</h1>

			<p>
				Lorem ipsum\t
				<a href=\".\">dolor</a>

				sit
				amet.
			</p>
		{/block}\n\n");

	$command->run($builder, ['file' => 'test.txt']);

	Assert::same(implode("\n", [
		'{block content}',
		'<h1>Lorem ipsum</h1>',
		'<p>',
		'Lorem ipsum',
		'<a href=".">dolor</a>',
		'sit',
		'amet.',
		'</p>',
		'{/block}',
		'',
	]), file_get_contents($path));

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'file'.");

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, ['file' => 'not-found.txt']);

	}, 'Deliverist\Builder\FileSystemException', "File 'not-found.txt' not found.");

});
