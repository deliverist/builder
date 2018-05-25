<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\ICommand;
	use Nette\Utils\FileSystem;


	class PingUrl implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string
		 * @param  bool
		 */
		public function run(Builder $builder, $url = NULL, $validateSsl = TRUE)
		{
			if (!isset($url)) {
				throw new InvalidArgumentException("Missing parameter 'url'.");
			}

			$options = array(
				'ssl' => array(
					'verify_peer' => $validateSsl,
					'verify_peer_name' => $validateSsl,
				),
			);

			$err = ($out = @file_get_contents($url, FALSE, stream_context_create($options))) === FALSE;

			if ($err) {
				$error = error_get_last();
				$builder->logError('Error type ' . $error['type']);
				$builder->logError($error['message']);
				$builder->logError('in file ' . $error['file'] . ':' . $error['line']);
				throw new InvalidStateException('URL is unreachable ' . $url);
			}

			$out = strip_tags($out);
			$lines = explode("\n", $out);

			foreach ($lines as $line) {
				$line = trim($line);

				if ($line === '') {
					continue;
				}

				$builder->log('> ' . $line);
			}
		}
	}
