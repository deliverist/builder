<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\ICommand;


	class ComposerInstall implements ICommand
	{
		/** @var string */
		private $executable = 'composer';


		/**
		 * @param  string
		 * @param  self
		 */
		public function setExecutable($executable)
		{
			$this->executable = $executable;
			return $this;
		}


		/**
		 * @param  Builder
		 * @param  string
		 */
		public function run(Builder $builder, $file = 'composer.json')
		{
			$builder->log('Running `composer install`');

			// http://stackoverflow.com/a/21921309
			$result = $builder->execute(array(
				$this->executable,
				'install',
				'--no-ansi',
				'--no-dev',
				'--no-interaction',
				'--no-progress',
				// '--no-scripts',
				'--optimize-autoloader',
				'--prefer-dist',
			), dirname($file));

			$builder->logDebug($result->toText());

			if (!$result->isOk()) {
				throw new InvalidStateException('Composer install failed.');
			}
		}
	}
