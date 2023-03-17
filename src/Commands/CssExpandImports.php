<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\Command;
	use Nette\Utils\Strings;


	class CssExpandImports implements Command
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
				$files = [$files];
			}

			foreach ($files as $file) {
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new FileSystemException("File '$file' not found.");
				}

				$this->processFile($path);
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

			file_put_contents($path, rtrim($this->expandCssImports($content, $path), "\n") . "\n");
		}


		/**
		 * @param  string $content
		 * @param  string $origFile
		 * @param  string $currentMediaQuery
		 * @return string
		 * @see    https://github.com/dg/ftp-deployment/blob/bf1cffb597896dd0d05cded01a9c3a16596c506d/src/Deployment/Preprocessor.php#L104
		 */
		private function expandCssImports($content, $origFile, $currentMediaQuery = NULL)
		{
			$dir = dirname($origFile);

			return (string) preg_replace_callback('#@import\s+(?:url)?[(\'"]+(.+)[)\'"]+(\s+.+)?;#U', function ($m) use ($dir, $currentMediaQuery) {
				$file = $dir . '/' . $m[1];

				if (!is_file($file)) {
					return $m[0];
				}

				$s = file_get_contents($file);

				if ($s === FALSE) {
					throw new \Deliverist\Builder\InvalidStateException("Reading of file $file failed.");
				}

				$newDir = dirname($file);
				$mediaQuery = isset($m[2]) ? $this->normalizeMediaQuery($m[2]) : NULL;

				if ($currentMediaQuery !== NULL && $mediaQuery !== $currentMediaQuery) {
					return $m[0];
				}

				$s = $this->expandCssImports($s, $file, $mediaQuery);

				if ($mediaQuery !== NULL && $mediaQuery !== $currentMediaQuery) {
					$s = '@media ' . $mediaQuery . " {\n"
						. $s
						. "}\n";
				}

				if ($newDir !== $dir) {
					$tmp = $dir . '/';

					if (substr($newDir, 0, strlen($tmp)) === $tmp) {
						$s = preg_replace('#\burl\(["\']?(?=[.\w])(?!\w+:)#', '$0' . substr($newDir, strlen($tmp)) . '/', $s);

						if ($s === NULL) {
							throw new \Deliverist\Builder\InvalidStateException("Replacing of content failed.");
						}

					} elseif (strpos($s, 'url(') !== FALSE) {
						return $m[0];
					}
				}

				return rtrim($s, "\n");

			}, $content);
		}


		/**
		 * @param  string $mediaQuery
		 * @return string|NULL
		 */
		private function normalizeMediaQuery($mediaQuery)
		{
			$mediaQuery = Strings::trim($mediaQuery);

			if ($mediaQuery === '' || $mediaQuery === 'all') {
				return NULL;
			}

			return $mediaQuery;
		}
	}
