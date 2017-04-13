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
	$command = new Commands\BowerInstall;

	$command->run($builder);
	$result = $builder->getRunnerResult();

	$command->setExecutable('/bin/bower');
	$command->run($builder, 'www/components/bower.json');
	$result = $builder->getRunnerResult();

	Assert::same(array(
		array('Running `bower install`', Builder::INFO),
		array("$ 'bower' 'install'\n\nDirectory: .\n\n=> 0\n\n", Builder::DEBUG),
		array('Running `bower install`', Builder::INFO),
		array("$ '/bin/bower' 'install'\n\nDirectory: www/components\n\n=> 0\n\n", Builder::DEBUG),
	), $log);
});
