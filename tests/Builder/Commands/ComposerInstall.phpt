<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../libs/TestBuilder.php';


test(function () {

	$log = [];
	$builder = new TestBuilder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = [$message, $type];
	};
	$command = new Commands\ComposerInstall;

	$command->run($builder);

	$command->setExecutable('/bin/composer');
	$command->run($builder, 'app/composer.json');

	Assert::same([
		['Running `composer install`', Builder::INFO],
		["$ 'composer' 'install' '--no-ansi' '--no-dev' '--no-interaction' '--no-progress' '--optimize-autoloader' '--prefer-dist'\n\nDirectory: .\n\n=> 0\n\n", Builder::DEBUG],
		['Running `composer install`', Builder::INFO],
		["$ '/bin/composer' 'install' '--no-ansi' '--no-dev' '--no-interaction' '--no-progress' '--optimize-autoloader' '--prefer-dist'\n\nDirectory: app\n\n=> 0\n\n", Builder::DEBUG],
	], $log);
});
