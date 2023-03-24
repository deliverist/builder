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
	$command = new Commands\PingUrl;

	file_put_contents(TEMP_DIR . '/index.html', "<html>
		<body>
			<p>Lorem ipsum</p>

			<p>
				dolor
				sit
				amet
			</p>
		</body>
		</html>");

	$command->run($builder, ['url' => TEMP_DIR . '/index.html'], FALSE);

	Assert::same([
		['> Lorem ipsum', Builder::INFO],
		['> dolor', Builder::INFO],
		['> sit', Builder::INFO],
		['> amet', Builder::INFO],
	], $log);

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'url'.");

});
