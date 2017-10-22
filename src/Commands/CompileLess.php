<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\CommandException;
	use Deliverist\Builder\ICommand;


	class CompileLess implements ICommand
	{
		/**
		 * @param  Builder
		 * @param  string|string[]
		 * @throws CompileLessException
		 */
		public function run(Builder $builder, $files = NULL)
		{
			if (!isset($files)) {
				throw new CompileLessException("Missing parameter 'files'.");
			}

			if (!is_array($files)) {
				$files = array($files);
			}

			foreach ($parameters as $file) {
				$path = $builder->getPath($file);

				if (!is_file($path)) {
					throw new CompileLessException("File '$file' not found");
				}

				$info = pathinfo($path);

				$res = $builder->execute(array(
					'lessc',
					'-ru',
					'--clean-css', // TODO: option??
					$path,
					$info['dirname'] . '/' . $info['filename'] . '.css',
				));

				if ($res->getCode() !== 0) {
					throw new CompileLessException("Compile LESS for file $file failed");
				}
			}
		}
	}


	class CompileLessException extends CommandException
	{
	}
