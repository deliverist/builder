<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;


	class GoogleClosureCompiler implements Command
	{
		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'files')) {
				$this->processFiles(
					$builder,
					Parameters::stringList($params, 'files')
				);

			} else {
				$this->processFiles(
					$builder,
					[Parameters::string($params, 'file')]
				);
			}
		}


		/**
		 * @param  string[] $files
		 * @return void
		 */
		public function processFiles(Builder $builder, array $files)
		{
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

			$output = @file_get_contents('https://closure-compiler.appspot.com/compile', FALSE, stream_context_create([
				'http' => [
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => 'output_info=compiled_code&js_code=' . urlencode($content),
				]
			]));

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
