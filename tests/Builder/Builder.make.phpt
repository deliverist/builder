<?php

declare(strict_types=1);

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
	$logger = new TestLogger;
	$builder = new Builder(TEMP_DIR, [
		'testCommand' => $testCommand,
	], $logger);

	$builder->make('testCommand');
	Assert::same([], $testCommand->args);
	Assert::same([
		'[START] testCommand',
		'[END] testCommand',
	], $logger->getLog());

	$builder->make('testCommand', ['arg1' => NULL, 'arg2' => 'TEST']);
	Assert::same(['arg1' => NULL, 'arg2' => 'TEST'], $testCommand->args);

});


test(function () { // closure

	$logger = new TestLogger;
	$closureArgs = [];
	$closure = function (Builder $builder, array $params) use (&$closureArgs) {
		$closureArgs = $params;
	};
	$builder = new Builder(TEMP_DIR, [
		'closureCmd' => $closure,
	], $logger);

	$builder->make('closureCmd');
	Assert::same([], $closureArgs);

	$builder->make('closureCmd', ['a' => 'ARG1', 'b' => 'ARG2']);
	Assert::same(['a' => 'ARG1', 'b' => 'ARG2'], $closureArgs);


	$builder->make($closure);
	Assert::same([], $closureArgs);

	$builder->make($closure, ['a' => 'ARG1', 'b' => 'ARG2']);
	Assert::same(['a' => 'ARG1', 'b' => 'ARG2'], $closureArgs);

	Assert::same([
		'[START] closureCmd',
		'[END] closureCmd',
		'[START] closureCmd',
		'[END] closureCmd',
		'[START] @anonymous',
		'[END] @anonymous',
		'[START] @anonymous',
		'[END] @anonymous',
	], $logger->getLog());
});


test(function () {

	$builder = new Builder(TEMP_DIR, [], new TestLogger);

	Assert::exception(function () use ($builder) {
		$builder->make('testCommand');
	}, 'Deliverist\Builder\BuilderException', "Missing command 'testCommand'.");

});
