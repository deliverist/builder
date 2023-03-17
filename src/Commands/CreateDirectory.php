<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\Command;
	use Nette\Utils\FileSystem;


	class CreateDirectory implements Command
	{
		/**
		 * @param  string|string[] $directories
		 * @throws InvalidArgumentException
		 */
		public function run(Builder $builder, $directories = NULL)
		{
			if (!isset($directories)) {
				throw new InvalidArgumentException("Missing parameter 'directories'.");
			}

			if (!is_array($directories)) {
				$directories = [$directories];
			}

			foreach ($directories as $directory) {
				$builder->log("Create directory '$directory'.");
				FileSystem::createDir($builder->getPath($directory));
			}
		}
	}
