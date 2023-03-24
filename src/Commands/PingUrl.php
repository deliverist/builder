<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;


	class PingUrl implements Command
	{
		public function run(Builder $builder, array $params)
		{
			$validateSsl = Parameters::bool($params, 'validateSsl', TRUE);

			if (Parameters::has($params, 'urls')) {
				foreach (Parameters::stringList($params, 'urls') as $url) {
					$this->processUrl($builder, $url, $validateSsl);
				}

			} else {
				$this->processUrl($builder, Parameters::string($params, 'url'), $validateSsl);
			}
		}


		/**
		 * @param  string $url
		 * @param  bool $validateSsl
		 * @return void
		 */
		public function processUrl(Builder $builder, $url, $validateSsl = TRUE)
		{
			$options = [
				'ssl' => [
					'verify_peer' => $validateSsl,
					'verify_peer_name' => $validateSsl,
				],
			];

			$err = ($out = @file_get_contents($url, FALSE, stream_context_create($options))) === FALSE;

			if ($err) {
				$error = error_get_last();

				if ($error !== NULL) {
					$builder->logError('Error type ' . $error['type']);
					$builder->logError($error['message']);
					$builder->logError('in file ' . $error['file'] . ':' . $error['line']);
				}

				throw new InvalidStateException('URL is unreachable ' . $url);
			}

			if (!is_string($out)) {
				throw new InvalidStateException("Reading of URL $url failed.");
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
