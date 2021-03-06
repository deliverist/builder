<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\ICommand;


	class CssExpandImports implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string|string[]
		 * @param  bool|NULL
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
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new FileSystemException("File '$file' not found.");
				}

				$this->processFile($path);
			}
		}


		private function processFile($path)
		{
			$content = file_get_contents($path);
			file_put_contents($path, $this->expandCssImports($content, $path));
		}


		/**
		 * @param  string
		 * @param  string
		 * @return string
		 * @see    https://github.com/dg/ftp-deployment/blob/bf1cffb597896dd0d05cded01a9c3a16596c506d/src/Deployment/Preprocessor.php#L104
		 */
		private function expandCssImports($content, $origFile)
		{
			$dir = dirname($origFile);

			return preg_replace_callback('#@import\s+(?:url)?[(\'"]+(.+)[)\'"]+;#U', function ($m) use ($dir) {
				$file = $dir . '/' . $m[1];

				if (!is_file($file)) {
					return $m[0];
				}

				$s = file_get_contents($file);
				$newDir = dirname($file);
				$s = $this->expandCssImports($s, $file);

				if ($newDir !== $dir) {
					$tmp = $dir . '/';

					if (substr($newDir, 0, strlen($tmp)) === $tmp) {
						$s = preg_replace('#\burl\(["\']?(?=[.\w])(?!\w+:)#', '$0' . substr($newDir, strlen($tmp)) . '/', $s);

					} elseif (strpos($s, 'url(') !== FALSE) {
						return $m[0];
					}
				}

				return $s;

			}, $content);
		}
	}
