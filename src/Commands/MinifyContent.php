<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;
	use Nette\Utils\FileSystem;


	class MinifyContent implements Command
	{
		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'files')) {
				$this->processFiles($builder, Parameters::stringList($params, 'files'));

			} else {
				$this->processFiles($builder, [Parameters::string($params, 'file')]);
			}
		}


		/**
		 * @param  string[] $files
		 * @return void
		 */
		public function processFiles(Builder $builder, array $files)
		{
			foreach ($files as $file) {
				$builder->log("Minify content of '$file'.");
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new FileSystemException("File '$file' not found.");
				}

				$lines = file($path);

				if ($lines === FALSE) {
					throw new \Deliverist\Builder\InvalidStateException("Reading of file $path failed.");
				}

				$lines = array_map('trim', $lines);
				$lines = array_filter($lines, function ($line) { // remove empty lines
					return $line !== '';
				});
				FileSystem::write($path, implode("\n", $lines) . "\n");
			}
		}
	}
