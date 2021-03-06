<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\ICommand;
	use Nette\Utils\FileSystem;


	class ReplaceContent implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string
		 * @param  array
		 */
		public function run(Builder $builder, $file = NULL, array $replacements = array())
		{
			if (!isset($file)) {
				throw new InvalidArgumentException("Missing parameter 'file'.");
			}

			$path = $builder->getPath($file);

			if (!is_file($path)) {
				throw new FileSystemException("File '$file' not found.");
			}

			$content = file_get_contents($path);
			$content = strtr($content, $replacements);
			file_put_contents($path, $content);
		}
	}
