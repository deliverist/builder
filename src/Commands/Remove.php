<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\Command;


	class Remove implements Command
	{
		/**
		 * @param  string|string[] $paths
		 */
		public function run(Builder $builder, $paths = [])
		{
			if (!is_array($paths)) {
				$paths = [$paths];
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
