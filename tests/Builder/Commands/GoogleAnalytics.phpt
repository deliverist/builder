<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$logger = new TestLogger;
	$builder = new Builder(TEMP_DIR, [], $logger);
	$command = new Commands\GoogleAnalytics;

	file_put_contents(TEMP_DIR . '/template.php', "\n%% GA %%\n");
	file_put_contents(TEMP_DIR . '/template.latte', "\n{* GA *}\n");
	file_put_contents(TEMP_DIR . '/template.html', "\n<!-- GA -->\n");

	$command->run($builder, [
		'file' => 'template.php',
		'code' => 'TEST-PHP',
		'placeholder' => '%% GA %%',
	]);
	$command->run($builder, [
		'file' => 'template.latte',
		'code' => 'TEST-LATTE',
	]);
	$command->run($builder, [
		'file' => 'template.html',
		'code' => 'TEST-HTML',
	]);

	Assert::same(implode('', [
		"\n",
		'<script>',
		'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
		"ga('create',\"TEST-PHP\",'auto');ga('send','pageview');",
		'</script>',
		'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
		"\n",
	]), file_get_contents(TEMP_DIR . '/template.php'));

	Assert::same(implode('', [
		"\n",
		'<script n:syntax="off">',
		'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
		"ga('create',\"TEST-LATTE\",'auto');ga('send','pageview');",
		'</script>',
		'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
		"\n",
	]), file_get_contents(TEMP_DIR . '/template.latte'));

	Assert::same(implode('', [
		"\n",
		'<script>',
		'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
		"ga('create',\"TEST-HTML\",'auto');ga('send','pageview');",
		'</script>',
		'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
		"\n",
	]), file_get_contents(TEMP_DIR . '/template.html'));

	Assert::same([
		"[INFO] Inserting Google Analytics code 'TEST-PHP' into 'template.php'.",
		"[INFO] Inserting Google Analytics code 'TEST-LATTE' into 'template.latte'.",
		"[INFO] Inserting Google Analytics code 'TEST-HTML' into 'template.html'.",
	], $logger->getLog());

});


test(function () {

	Tester\Helpers::purge(TEMP_DIR);
	$builder = new Builder(TEMP_DIR, [], new TestLogger);
	$command = new Commands\GoogleAnalytics;

	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, []);
	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'file'.");


	Assert::exception(function () use ($command, $builder) {
		$command->run($builder, ['file' => 'file.txt']);
	}, Deliverist\Builder\MissingParameterException::class, "Missing parameter 'code'.");


	Assert::exception(function () use ($command, $builder) {
		file_put_contents(TEMP_DIR . '/file.txt', '[GA]');
		$command->run($builder, ['file' => 'file.txt', 'code' => 'TEST-TXT']);
	}, 'Deliverist\Builder\InvalidArgumentException', "Missing placeholder, unknow file extension 'txt'.");

});
