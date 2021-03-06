<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\ICommand;
	use Nette\Utils\FileSystem;


	class MinifyContent implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string|string[]
		 */
		public function run(Builder $builder, $files = NULL)
		{
			if (!isset($files)) {
				throw new InvalidArgumentException("Missing parameter 'files'.");
			}

			if (!is_array($files)) {
				$files = array($files);
			}

			foreach ($files as $file) {
				$builder->log("Minify content of '$file'.");
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new FileSystemException("File '$file' not found.");
				}

				$lines = file($path);
				$lines = array_map('trim', $lines);
				$lines = array_filter($lines, function ($line) { // remove empty lines
					return $line !== '';
				});
				FileSystem::write($path, implode("\n", $lines) . "\n");
			}
		}
	}
