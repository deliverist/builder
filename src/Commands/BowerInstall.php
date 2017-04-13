<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\ICommand;
	use Deliverist\Builder\CommandException;


	class BowerInstall implements ICommand
	{
		/** @var string */
		private $executable = 'bower';


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
		 * @throws BowerInstallException
		 */
		public function run(Builder $builder, $file = 'bower.json')
		{
			$builder->log('Running `bower install`');
			$result = $builder->execute(array(
				$this->executable,
				'install',
			), dirname($file));

			$builder->logDebug($result->toText());

			if (!$result->isOk()) {
				throw new BowerInstallException('Bower install failed.');
			}
		}
	}


	class BowerInstallException extends CommandException
	{
	}
