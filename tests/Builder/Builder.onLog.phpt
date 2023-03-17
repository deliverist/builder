<?php

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {

	$output = [];
	$builder = new Builder(__DIR__);
	$builder->onLog[] = function ($message, $type) use (&$output) {
		$output[] = "$type: $message";
	};

	$builder->log("Lorem\nIpsum\n");
	$builder->logDebug('DEBUG');
	$builder->logWarning('WARNING');
	$builder->logError('ERROR');
	$builder->logSuccess('SUCCESS');

	Assert::same([
		Builder::INFO . ": Lorem\nIpsum\n",
		Builder::DEBUG . ': DEBUG',
		Builder::WARNING . ': WARNING',
		Builder::ERROR . ': ERROR',
		Builder::SUCCESS . ': SUCCESS',
	], $output);

});
