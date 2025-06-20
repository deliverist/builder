<?php

declare(strict_types=1);

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	$logger = new TestLogger;
	$builder = new TestBuilder(TEMP_DIR, [], $logger);

	$command = new Commands\ComposerInstall;
	$command->run($builder, []);

	$command = new Commands\ComposerInstall('/bin/composer');
	$command->run($builder, ['composerFile' => 'app/composer.json']);

	Assert::same([
		'[INFO] Running `composer install`',
		"[DEBUG] $ 'composer' 'install' '--no-ansi' '--no-dev' '--no-interaction' '--no-progress' '--optimize-autoloader' '--prefer-dist'\n\nDirectory: .\n\n=> 0\n\n",
		'[INFO] Running `composer install`',
		"[DEBUG] $ '/bin/composer' 'install' '--no-ansi' '--no-dev' '--no-interaction' '--no-progress' '--optimize-autoloader' '--prefer-dist'\n\nDirectory: app\n\n=> 0\n\n",
	], $logger->getLog());
});
