<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\ICommand;


	class BowerInstall implements ICommand
	{
		/** @var string */
		private $executable = 'bower';


		/**
		 * @param  string $executable
		 * @return self
		 */
		public function setExecutable($executable)
		{
			$this->executable = $executable;
			return $this;
		}


		/**
		 * @param  string $file
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
				throw new InvalidStateException('Bower install failed.');
			}
		}
	}
