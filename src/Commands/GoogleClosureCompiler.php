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
		 * @param  string|string[] $files
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


		/**
		 * @param  string $path
		 * @return void
		 */
		private function compressFile($path)
		{
			$content = file_get_contents($path);

			if ($content === FALSE) {
				throw new \Deliverist\Builder\InvalidStateException("Reading of file $path failed.");
			}

			$output = @file_get_contents('https://closure-compiler.appspot.com/compile', FALSE, stream_context_create(array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => 'output_info=compiled_code&js_code=' . urlencode($content),
				)
			)));

			if (!is_string($output)) {
				$error = error_get_last();

				if ($error !== NULL) {
					throw new InvalidStateException("Unable to minify: {$error['message']}");
				}

				throw new InvalidStateException("Unable to minify");
			}

			file_put_contents($path, $output);
		}
	}
