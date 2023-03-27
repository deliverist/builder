<?php

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {

	$logger = new TestLogger;
	$builder = new Builder(__DIR__, [], $logger);

	$builder->log("Lorem\nIpsum\n");
	$builder->logDebug('DEBUG');
	$builder->logWarning('WARNING');
	$builder->logError('ERROR');
	$builder->logSuccess('SUCCESS');

	Assert::same([
		"[INFO] Lorem\nIpsum\n",
		'[DEBUG] DEBUG',
		'[WARNING] WARNING',
		'[ERROR] ERROR',
		'[SUCCESS] SUCCESS',
	], $logger->getLog());

});
