<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\ICommand;


	class Remove implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string|string[]
		 */
		public function run(Builder $builder, $paths = array())
		{
			if (!is_array($paths)) {
				$paths = array($paths);
			}

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
