<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;


	class ComposerInstall implements Command
	{
		/** @var string */
		private $executable;


		/**
		 * @param  string $executable
		 */
		public function __construct($executable = 'composer')
		{
			$this->executable = $executable;
		}


		public function run(Builder $builder, array $params)
		{
			$this->processInstall(
				$builder,
				Parameters::string($params, 'composerFile', 'composer.json')
			);
		}


		/**
		 * @param  string $file
		 * @return void
		 */
		public function processInstall(Builder $builder, $file = 'composer.json')
		{
			$builder->log('Running `composer install`');

			// http://stackoverflow.com/a/21921309
			$result = $builder->execute([
				$this->executable,
				'install',
				'--no-ansi',
				'--no-dev',
				'--no-interaction',
				'--no-progress',
				// '--no-scripts',
				'--optimize-autoloader',
				'--prefer-dist',
			], dirname($file));

			$builder->logDebug($result->toText());

			if (!$result->isOk()) {
				throw new InvalidStateException('Composer install failed.');
			}
		}
	}
