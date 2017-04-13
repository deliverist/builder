<?php

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


class TestCommand implements Deliverist\Builder\ICommand
{
	public $args;


	public function run(Deliverist\Builder\Builder $builder, $argument1 = NULL, $argument2 = NULL)
	{
		$this->args = array($argument1, $argument2);
	}
}


test(function () {

	$testCommand = new TestCommand;
	$makeLog = array();
	$builder = new Builder(TEMP_DIR, array(
		'testCommand' => $testCommand,
	));
	$builder->onMake[] = function ($commandName, $type) use (&$makeLog) {
		$makeLog[] = array($commandName, $type);
	};

	$builder->make('testCommand');
	Assert::same(array(NULL, NULL), $testCommand->args);
	Assert::same(array(
		array('testCommand', Builder::MAKE_START),
		array('testCommand', Builder::MAKE_END),
	), $makeLog);

	$builder->make('testCommand', NULL, 'TEST');
	Assert::same(array(NULL, 'TEST'), $testCommand->args);

});


test(function () { // closure

	$makeLog = array();
	$closureArgs = array();
	$closure = function (Builder $builder, $arg = NULL) use (&$closureArgs) {
		$closureArgs = func_get_args();
		array_shift($closureArgs);
	};
	$builder = new Builder(TEMP_DIR, array(
		'closureCmd' => $closure,
	));
	$builder->onMake[] = function ($commandName, $type) use (&$makeLog) {
		$makeLog[] = array($commandName, $type);
	};


	$builder->make('closureCmd');
	Assert::same(array(), $closureArgs);
	Assert::same(array(
		array('closureCmd', Builder::MAKE_START),
		array('closureCmd', Builder::MAKE_END),
	), $makeLog);


	$builder->make('closureCmd', 'ARG1', 'ARG2');
	Assert::same(array('ARG1', 'ARG2'), $closureArgs);


	$makeLog = array();
	$builder->make($closure);
	Assert::same(array(), $closureArgs);
	Assert::same(array(
		array('callback', Builder::MAKE_START),
		array('callback', Builder::MAKE_END),
	), $makeLog);


	$builder->make($closure, 'ARG1', 'ARG2');
	Assert::same(array('ARG1', 'ARG2'), $closureArgs);

});


test(function () {

	$builder = new Builder(TEMP_DIR);

	Assert::exception(function () use ($builder) {
		$builder->make('testCommand');
	}, 'Deliverist\Builder\BuilderException', "Missing command 'testCommand'.");

});
