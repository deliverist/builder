<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;
	use Nette\Utils\FileSystem;


	class ReplaceContent implements Command
	{
		public function run(Builder $builder, array $params)
		{
			$replacements = Parameters::stringMap($params, 'replacements');

			if (Parameters::has($params, 'files')) {
				foreach (Parameters::stringList($params, 'files') as $file) {
					$this->processFile($builder, $file, $replacements);
				}

			} else {
				$this->processFile($builder, Parameters::string($params, 'file'), $replacements);
			}
		}


		/**
		 * @param  string $file
		 * @param  array<string, string> $replacements
		 * @return void
		 */
		public function processFile(Builder $builder, $file = NULL, array $replacements = [])
		{
			if (!isset($file)) {
				throw new InvalidArgumentException("Missing parameter 'file'.");
			}

			$path = $builder->getPath($file);

			if (!is_file($path)) {
				throw new FileSystemException("File '$file' not found.");
			}

			$content = file_get_contents($path);

			if ($content === FALSE) {
				throw new \Deliverist\Builder\InvalidStateException("Reading of file $path failed.");
			}

			$content = strtr($content, $replacements);
			file_put_contents($path, $content);
		}
	}
