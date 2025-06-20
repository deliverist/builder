<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {

	$builder = new TestBuilder(TEMP_DIR, [], new TestLogger);
	$result = $builder->execute([
		'rm',
		'-rf',
		'/',
	]);

	Assert::same("'rm' '-rf' '/'", $result->getCommand());
	Assert::same(["Directory: "], $result->getOutput());

});
