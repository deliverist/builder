<?php

declare(strict_types=1);

use Deliverist\Builder\Builder;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {

	$builder = new Builder(TEMP_DIR, [], new TestLogger);

	Assert::exception(function () use ($builder) {
		$builder->readFile('docs/readme.txt');
	}, 'Nette\IOException');

	$content = "Readme\nLorem ipsum dolor sit amet.\n";
	$builder->writeFile('docs/readme.txt', $content);

	Assert::same($content, $builder->readFile('docs/readme.txt'));

	$builder->delete('docs');
	Assert::false(file_exists(TEMP_DIR . '/docs/readme.txt'));

});
