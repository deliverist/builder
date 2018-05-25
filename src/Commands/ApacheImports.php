<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\ICommand;


	class ApacheImports implements ICommand
	{
		/** @var array */
		private $toRemove;


		/**
		 * @param  Builder
		 * @param  string|string[]
		 * @param  bool|NULL
		 */
		public function run(Builder $builder, $files = NULL, $removeFiles = TRUE)
		{
			if (!isset($files)) {
				throw new InvalidArgumentException("Missing parameter 'files'.");
			}

			$this->toRemove = array();

			if (!is_array($files)) {
				$files = array($files);
			}

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


		private function processFile($path)
		{
			$content = file_get_contents($path);
			file_put_contents($path, $this->expandApacheImports($content, $path));
		}


		private function expandApacheImports($content, $path)
		{
			$dir = dirname($path);
			return preg_replace_callback('~<!--#include\s+file="(.+)"\s+-->~U', function ($m) use ($dir, $path) {
				$file = $dir . '/' . $m[1];

				if (is_file($file)) {
					$this->toRemove[] = $file;
					return $this->expandApacheImports(file_get_contents($file), $file);

				} else {
					throw new FileSystemException("Required file '" . $m[1] . "' is missing.");
				}

			}, $content);
		}
	}
