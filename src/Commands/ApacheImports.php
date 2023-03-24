<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;


	class ApacheImports implements Command
	{
		/** @var string[] */
		private $toRemove;


		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'files')) {
				$this->processFiles(
					$builder,
					Parameters::stringList($params, 'files'),
					Parameters::bool($params, 'removeFiles')
				);

			} else {
				$this->processFiles(
					$builder,
					[Parameters::string($params, 'file')],
					Parameters::bool($params, 'removeFiles', TRUE)
				);
			}
		}


		/**
		 * @param  string[] $files
		 * @param  bool|NULL $removeFiles
		 * @return void
		 */
		public function processFiles(Builder $builder, array $files, $removeFiles = TRUE)
		{
			$this->toRemove = [];

			foreach ($files as $file) {
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new FileSystemException("File '$file' not found.");
				}

				$this->processFile($path);
			}

			if ($removeFiles) {
				foreach ($this->toRemove as $file) {
					\Nette\Utils\FileSystem::delete($file);
				}
			}
		}


		/**
		 * @param  string $path
		 * @return void
		 */
		private function processFile($path)
		{
			$content = file_get_contents($path);

			if ($content === FALSE) {
				throw new \Deliverist\Builder\InvalidStateException("Reading of file $path failed.");
			}

			file_put_contents($path, $this->expandApacheImports($content, $path));
		}


		/**
		 * @param  string $content
		 * @param  string $path
		 * @return string
		 */
		private function expandApacheImports($content, $path)
		{
			$dir = dirname($path);
			return (string) preg_replace_callback('~<!--#include\s+file="(.+)"\s+-->~U', function ($m) use ($dir) {
				$file = $dir . '/' . $m[1];

				if (is_file($file)) {
					$this->toRemove[] = $file;
					$fileContent = file_get_contents($file);

					if ($fileContent === FALSE) {
						throw new \Deliverist\Builder\InvalidStateException("Reading of file $file failed.");
					}

					return $this->expandApacheImports($fileContent, $file);

				} else {
					throw new FileSystemException("Required file '" . $m[1] . "' is missing.");
				}

			}, $content);
		}
	}
