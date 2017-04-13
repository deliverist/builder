<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\ICommand;
	use Deliverist\Builder\CommandException;


	class GoogleAnalytics implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string
		 * @param  string
		 * @param  string|NULL
		 */
		public function run(Builder $builder, $file = NULL, $code = NULL, $placeholder = NULL)
		{
			$builder->log("Inserting Google Analytics code '$code' into '$file'.");

			if (!is_string($file)) {
				throw new GoogleAnalyticsException('File must be string, ' . gettype($file) . ' given.');
			}

			if (!is_string($code)) {
				throw new GoogleAnalyticsException('Code must be string, ' . gettype($code) . ' given.');
			}

			$parameters = $this->prepareParameters($file, $placeholder);
			$script = array(
				'<script' . (isset($parameters['attributes']) ? (' ' . $parameters['attributes']) : '') . '>',
				'ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;',
				"ga('create'," . json_encode((string) $code) . ",'auto');ga('send','pageview');",
				'</script>',
				'<script src="https://www.google-analytics.com/analytics.js" async defer></script>',
			);

			$content = $builder->readFile($file);
			$content = strtr($content, array(
				$parameters['placeholder'] => implode('', $script),
			));
			$builder->writeFile($file, $content);
		}


		/**
		 * @param  string
		 * @param  string|NULL
		 * @return array
		 */
		private function prepareParameters($file, $placeholder)
		{
			$parameters = array(
				'attributes' => NULL,
				'placeholder' => NULL,
			);

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
				throw new GoogleAnalyticsException("Missing placeholder, unknow file extension '$extension'.");
			}

			return $parameters;
		}
	}


	class GoogleAnalyticsException extends CommandException
	{
	}
