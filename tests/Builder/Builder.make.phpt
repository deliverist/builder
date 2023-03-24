<?php

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


class TestCommand implements Deliverist\Builder\Command
{
	public $args;


	public function run(Deliverist\Builder\Builder $builder, array $params)
	{
		$this->args = $params;
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
	Assert::same([], $testCommand->args);
	Assert::same([
		['testCommand', Builder::MAKE_START],
		['testCommand', Builder::MAKE_END],
	], $makeLog);

	$builder->make('testCommand', ['arg1' => NULL, 'arg2' => 'TEST']);
	Assert::same(['arg1' => NULL, 'arg2' => 'TEST'], $testCommand->args);

});


test(function () { // closure

	$makeLog = [];
	$closureArgs = [];
	$closure = function (Builder $builder, array $params) use (&$closureArgs) {
		$closureArgs = $params;
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


	$builder->make('closureCmd', ['a' => 'ARG1', 'b' => 'ARG2']);
	Assert::same(['a' => 'ARG1', 'b' => 'ARG2'], $closureArgs);


	$makeLog = [];
	$builder->make($closure);
	Assert::same([], $closureArgs);
	Assert::same([
		['callback', Builder::MAKE_START],
		['callback', Builder::MAKE_END],
	], $makeLog);


	$builder->make($closure, ['a' => 'ARG1', 'b' => 'ARG2']);
	Assert::same(['a' => 'ARG1', 'b' => 'ARG2'], $closureArgs);

});


test(function () {

	$builder = new Builder(TEMP_DIR);

	Assert::exception(function () use ($builder) {
		$builder->make('testCommand');
	}, 'Deliverist\Builder\BuilderException', "Missing command 'testCommand'.");

});
