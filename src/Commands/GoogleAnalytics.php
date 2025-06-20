<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;


	class GoogleAnalytics implements Command
	{
		public function run(Builder $builder, array $params)
		{
			$this->processFile(
				$builder,
				Parameters::string($params, 'file'),
				Parameters::string($params, 'code'),
				Parameters::stringOrNull($params, 'placeholder')
			);
		}


		/**
		 * @param  string $file
		 * @param  string $code
		 * @param  string|NULL $placeholder
		 * @return void
		 */
		public function processFile(Builder $builder, $file, $code, $placeholder = NULL)
		{
			$builder->log("Inserting Google Analytics code '$code' into '$file'.");

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
