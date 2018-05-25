<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$log = array();
	$builder = new Builder(TEMP_DIR);
	$builder->onLog[] = function ($message, $type) use (&$log) {
		$log[] = array($message, $type);
	};
	$command = new Commands\GoogleAnalytics;

	file_put_contents(TEMP_DIR . '/template.php', "\n%% GA %%\n");
	file_put_contents(TEMP_DIR . '/template.latte', "\n{* GA *}\n");
	file_put_contents(TEMP_DIR . '/template.html', "\n<!-- GA -->\n");

	$command->run($builder, 'template.php', 'TEST-PHP', '%% GA %%');
	$command->run($builder, 'template.latte', 'TEST-LATTE');
	$command->run($builder, 'template.html', 'TEST-HTML');

	Assert::same(implode('', array(
		"\n",
		'<script>',
		'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
		"ga('create',\"TEST-PHP\",'auto');ga('send','pageview');",
		'</script>',
		'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
		"\n",
	)), file_get_contents(TEMP_DIR . '/template.php'));

	Assert::same(implode('', array(
		"\n",
		'<script n:syntax="off">',
		'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
		"ga('create',\"TEST-LATTE\",'auto');ga('send','pageview');",
		'</script>',
		'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
		"\n",
	)), file_get_contents(TEMP_DIR . '/template.latte'));

	Assert::same(implode('', array(
		"\n",
		'<script>',
		'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
		"ga('create',\"TEST-HTML\",'auto');ga('send','pageview');",
		'</script>',
		'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
		"\n",
	)), file_get_contents(TEMP_DIR . '/template.html'));

	Assert::same(array(
		array("Inserting Google Analytics code 'TEST-PHP' into 'template.php'.", Builder::INFO),
		array("Inserting Google Analytics code 'TEST-LATTE' into 'template.latte'.", Builder::INFO),
		array("Inserting Google Analytics code 'TEST-HTML' into 'template.html'.", Builder::INFO),
	), $log);

});


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR);
	$command = new Commands\GoogleAnalytics;

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder);
	}, 'Deliverist\Builder\InvalidArgumentException', 'File must be string, NULL given.');


	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, 'file.txt');
	}, 'Deliverist\Builder\InvalidArgumentException', 'Code must be string, NULL given.');


	Assert::exception(function () use ($command, $builder) {
		file_put_contents(TEMP_DIR . '/file.txt', '[GA]');
		$command->run($builder, 'file.txt', 'TEST-TXT');
	}, 'Deliverist\Builder\InvalidArgumentException', "Missing placeholder, unknow file extension 'txt'.");

});
