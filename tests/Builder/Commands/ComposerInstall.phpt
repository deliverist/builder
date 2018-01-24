<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/../../libs/TestBuilder.php';


test(function () {

	$log = array();
	$builder = new TestBuilder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = array($message, $type);
	};
	$command = new Commands\ComposerInstall;

	$command->run($builder);

	$command->setExecutable('/bin/composer');
	$command->run($builder, 'app/composer.json');

	Assert::same(array(
		array('Running `composer install`', Builder::INFO),
		array("$ 'composer' 'install' '--no-ansi' '--no-dev' '--no-interaction' '--no-progress' '--optimize-autoloader' '--prefer-dist'\n\nDirectory: .\n\n=> 0\n\n", Builder::DEBUG),
		array('Running `composer install`', Builder::INFO),
		array("$ '/bin/composer' 'install' '--no-ansi' '--no-dev' '--no-interaction' '--no-progress' '--optimize-autoloader' '--prefer-dist'\n\nDirectory: app\n\n=> 0\n\n", Builder::DEBUG),
	), $log);
});
