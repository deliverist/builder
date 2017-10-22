<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\CommandException;
	use Deliverist\Builder\ICommand;


	class ApacheImports implements ICommand
	{
		/** @var array */
		private $toRemove;


		/**
		 * @param  Builder
		 * @param  string|string[]
		 * @param  bool|NULL
		 * @throws ApacheImportsException
		 */
		public function run(Builder $builder, $files = NULL, $removeFiles = NULL)
		{
			if (!isset($files)) {
				throw new ApacheImportsException("Missing parameter 'files'.");
			}

			$this->toRemove = array();
			$removeFiles = isset($removeFiles) ? $removeFiles : TRUE;

			if (!is_array($files)) {
				$files = array($files);$this->toRemove = array();
			}

			foreach ($files as $file) {
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new ApacheImportsException("File '$file' not found");
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
			return preg_replace_callback('~<!--#include\s+file="(.+)"\s+-->~U', function ($m) use ($dir) {
				$file = $dir . '/' . $m[1];

				if (is_file($file)) {
					$this->toRemove[] = $file;
					return $this->expandApacheImports(file_get_contents($file), dirname($file));

				} else {
					throw new ApacheImportsException("Required file '" . $m[1] . "' is missing");
				}
				return $m[0];
			}, $content);
		}
	}


	class ApacheImportsException extends CommandException
	{
	}
