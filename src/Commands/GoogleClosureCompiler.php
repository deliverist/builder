<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\ICommand;


	class GoogleClosureCompiler implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string|string[]
		 * @param  string|NULL
		 */
		public function run(Builder $builder, $files = NULL)
		{
			if (!isset($files)) {
				throw new InvalidArgumentException("Missing parameter 'files'.");
			}

			if (!is_array($files)) {
				$files = (array) $files;
			}

			foreach ($files as $file) {
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new FileSystemException("File '$file' not found.");
				}

				$this->compressFile($path);
			}
		}


		private function compressFile($path)
		{
			$content = file_get_contents($path);
			$output = @file_get_contents('https://closure-compiler.appspot.com/compile', FALSE, stream_context_create(array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => 'output_info=compiled_code&js_code=' . urlencode($content),
				)
			)));

			if (!is_string($output)) {
				$error = error_get_last();
				throw new InvalidStateException("Unable to minfy: {$error['message']}\n");
			}

			file_put_contents($path, $output);
		}
	}
