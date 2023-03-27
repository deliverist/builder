<?php

/**
 * @phpVersion >= 8.0
 */

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () { // named

	$closureArgs = [];
	$command = function (Builder $builder, array $params) use (&$closureArgs) {
		$closureArgs = $params;
	};
	$builder = new Builder(TEMP_DIR, [
		'command' => $command,
	], new TestLogger);


	$builder->make('command');
	Assert::same([], $closureArgs);

	$builder->make('command', ['arg' => 'ARG1', 'arg2' => 'ARG2']);
	Assert::same(['arg' => 'ARG1', 'arg2' => 'ARG2'], $closureArgs);

	$builder->make('command', arg: 'ARG1');
	Assert::same(['arg' => 'ARG1'], $closureArgs);

	$builder->make('command', arg2: 'ARG2');
	Assert::same(['arg2' => 'ARG2'], $closureArgs);

	$builder->make('command', arg: 'ARG1', arg2: 'ARG2');
	Assert::same(['arg' => 'ARG1', 'arg2' => 'ARG2'], $closureArgs);

});
