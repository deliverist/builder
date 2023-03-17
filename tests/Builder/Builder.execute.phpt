<?php

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../libs/TestBuilder.php';


test(function () {

	$builder = new TestBuilder(TEMP_DIR);
	$result = $builder->execute([
		'rm',
		'-rf',
		'/',
	]);

	Assert::same("'rm' '-rf' '/'", $result->getCommand());
	Assert::same(["Directory: "], $result->getOutput());

});
