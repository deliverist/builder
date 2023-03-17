<?php

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


class TestCommand implements Deliverist\Builder\ICommand
{
	public $args;


	public function run(Deliverist\Builder\Builder $builder, $argument1 = NULL, $argument2 = NULL)
	{
		$this->args = [$argument1, $argument2];
	}
}


test(function () {

	$testCommand = new TestCommand;
	$makeLog = [];
	$builder = new Builder(TEMP_DIR, [
		'testCommand' => $testCommand,
	]);
	$builder->onMake[] = function ($commandName, $type) use (&$makeLog) {
		$makeLog[] = [$commandName, $type];
	};

	$builder->make('testCommand');
	Assert::same([NULL, NULL], $testCommand->args);
	Assert::same([
		['testCommand', Builder::MAKE_START],
		['testCommand', Builder::MAKE_END],
	], $makeLog);

	$builder->make('testCommand', NULL, 'TEST');
	Assert::same([NULL, 'TEST'], $testCommand->args);

});


test(function () { // closure

	$makeLog = [];
	$closureArgs = [];
	$closure = function (Builder $builder, $arg = NULL) use (&$closureArgs) {
		$closureArgs = func_get_args();
		array_shift($closureArgs);
	};
	$builder = new Builder(TEMP_DIR, [
		'closureCmd' => $closure,
	]);
	$builder->onMake[] = function ($commandName, $type) use (&$makeLog) {
		$makeLog[] = [$commandName, $type];
	};


	$builder->make('closureCmd');
	Assert::same([], $closureArgs);
	Assert::same([
		['closureCmd', Builder::MAKE_START],
		['closureCmd', Builder::MAKE_END],
	], $makeLog);


	$builder->make('closureCmd', 'ARG1', 'ARG2');
	Assert::same(['ARG1', 'ARG2'], $closureArgs);


	$makeLog = [];
	$builder->make($closure);
	Assert::same([], $closureArgs);
	Assert::same([
		['callback', Builder::MAKE_START],
		['callback', Builder::MAKE_END],
	], $makeLog);


	$builder->make($closure, 'ARG1', 'ARG2');
	Assert::same(['ARG1', 'ARG2'], $closureArgs);

});


test(function () {

	$builder = new Builder(TEMP_DIR);

	Assert::exception(function () use ($builder) {
		$builder->make('testCommand');
	}, 'Deliverist\Builder\BuilderException', "Missing command 'testCommand'.");

});
