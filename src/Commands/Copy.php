<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;
	use Nette\Utils\FileSystem;


	class Copy implements Command
	{
		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'files')) {
				$this->process($builder, Parameters::stringMap($params, 'files'));

			} elseif (Parameters::has($params, 'paths')) {
				$this->process($builder, Parameters::stringMap($params, 'paths'));

			} else {
				$source = Parameters::string($params, 'from');
				$destination = Parameters::string($params, 'to');
				$this->process($builder, [
					$source => $destination,
				]);
			}
		}


		/**
		 * @param  string[] $paths
		 * @return void
		 */
		public function process(Builder $builder, array $paths)
		{
			foreach ($paths as $sourcePath => $destinationPath) {
				$builder->log("Copying '$sourcePath' destination '$destinationPath'.");
				FileSystem::copy($builder->getPath($sourcePath), $builder->getPath($destinationPath), FALSE);
			}
		}
	}
