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
		'[INFO] > Lorem ipsum',
		'[INFO] > dolor',
		'[INFO] > sit',
		'[INFO] > amet',
	], $logger->getLog());

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);

	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'url'.");

});
