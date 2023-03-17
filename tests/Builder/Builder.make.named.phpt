<?php

/**
 * @phpVersion >= 8.0
 */

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () { // named

	$closureArgs = [];
	$command = function (Builder $builder, $arg = NULL, $arg2 = NULL) use (&$closureArgs) {
		$closureArgs = func_get_args();
		array_shift($closureArgs);
	};
	$builder = new Builder(TEMP_DIR, [
		'command' => $command,
	]);
	$builder->onMake[] = function ($commandName, $type) use (&$makeLog) {
		$makeLog[] = [$commandName, $type];
	};


	$builder->make('command');
	Assert::same([], $closureArgs);

	$builder->make('command', 'ARG1', 'ARG2');
	Assert::same(['ARG1', 'ARG2'], $closureArgs);

	$builder->make('command', arg: 'ARG1');
	Assert::same(['ARG1'], $closureArgs);

	$builder->make('command', arg2: 'ARG2');
	Assert::same([NULL, 'ARG2'], $closureArgs);

	$builder->make('command', arg: 'ARG1', arg2: 'ARG2');
	Assert::same(['ARG1', 'ARG2'], $closureArgs);

	$builder->make('command', arg: 'ARG1', arg2: 'ARG2');
	Assert::same(['ARG1', 'ARG2'], $closureArgs);

});
