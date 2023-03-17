<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\ICommand;
	use Nette\Utils\FileSystem;


	class Copy implements ICommand
	{
		/**
		 * @param  string|string[] $source
		 * @param  string|NULL $destination
		 */
		public function run(Builder $builder, $source = NULL, $destination = NULL)
		{
			if (!isset($source)) {
				throw new InvalidArgumentException("Missing parameter 'source'.");
			}

			$paths = [];

			if (is_array($source)) {
				$paths = $source;

			} else {
				if ($destination === NULL) {
					throw new InvalidArgumentException("Missing parameter 'destination'.");
				}

				$paths[$source] = $destination;
			}

			foreach ($paths as $sourcePath => $destinationPath) {
				$builder->log("Copying '$sourcePath' destination '$destinationPath'.");
				FileSystem::copy($builder->getPath($sourcePath), $builder->getPath($destinationPath), FALSE);
			}
		}
	}
