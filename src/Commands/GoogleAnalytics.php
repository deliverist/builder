<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\Command;


	class GoogleAnalytics implements Command
	{
		/**
		 * @param  string $file
		 * @param  string $code
		 * @param  string|NULL $placeholder
		 */
		public function run(Builder $builder, $file = NULL, $code = NULL, $placeholder = NULL)
		{
			$builder->log("Inserting Google Analytics code '$code' into '$file'.");

			if (!is_string($file)) {
				throw new InvalidArgumentException('File must be string, ' . gettype($file) . ' given.');
			}

			if (!is_string($code)) {
				throw new InvalidArgumentException('Code must be string, ' . gettype($code) . ' given.');
			}

			$parameters = $this->prepareParameters($file, $placeholder);
			$script = [
				'<script' . (isset($parameters['attributes']) ? (' ' . $parameters['attributes']) : '') . '>',
				'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
				"ga('create'," . json_encode((string) $code) . ",'auto');ga('send','pageview');",
				'</script>',
				'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
			];

			$content = $builder->readFile($file);
			$content = strtr($content, [
				$parameters['placeholder'] => implode('', $script),
			]);
			$builder->writeFile($file, $content);
		}


		/**
		 * @param  string $file
		 * @param  string|NULL $placeholder
		 * @return array<string, string|NULL>
		 */
		private function prepareParameters($file, $placeholder)
		{
			$parameters = [
				'attributes' => NULL,
				'placeholder' => NULL,
			];

			$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

			if ($extension === 'latte') {
				$parameters['placeholder'] = '{* GA *}';
				$parameters['attributes'] = 'n:syntax="off"';

			} elseif ($extension === 'html') {
				$parameters['placeholder'] = '<!-- GA -->';
			}

			if ($parameters['placeholder'] === NULL) {
				$parameters['placeholder'] = $placeholder;
			}

			if ($parameters['placeholder'] === NULL) {
				throw new InvalidArgumentException("Missing placeholder, unknow file extension '$extension'.");
			}

			return $parameters;
		}
	}
