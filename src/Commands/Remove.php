<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;


	class Remove implements Command
	{
		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'paths')) {
				$this->processPaths($builder, Parameters::stringList($params, 'paths'));

			} elseif (Parameters::has($params, 'files')) {
				$this->processPaths($builder, Parameters::stringList($params, 'files'));

			} elseif (Parameters::has($params, 'path')) {
				$this->processPaths($builder, [Parameters::string($params, 'path')]);

			} else {
				$this->processPaths($builder, [Parameters::string($params, 'file')]);
			}
		}


		/**
		 * @param  string[] $paths
		 * @return void
		 */
		public function processPaths(Builder $builder, array $paths)
		{
			foreach ($paths as $path) {
				if (!file_exists($builder->getPath($path))) {
					$builder->logWarning("Path '$path' not found.");
					continue;
				}

				$builder->log("Removing path '$path'.");
				$builder->delete($path);
			}
		}
	}
