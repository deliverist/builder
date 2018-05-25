<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\ICommand;
	use Nette\Utils\FileSystem;


	class LessCompile implements ICommand
	{
		/** @var string */
		private $executable = 'lessc';


		/**
		 * @param  string
		 * @param  self
		 */
		public function setExecutable($executable)
		{
			$this->executable = $executable;
			return $this;
		}


		/**
		 * @param  Builder
		 * @param  string
		 */
		public function run(Builder $builder, $file = NULL)
		{
			if (!isset($file)) {
				throw new InvalidArgumentException("Missing parameter 'file'.");
			}

			$path = $builder->getPath($file);

			if (!is_file($path)) {
				throw new FileSystemException("File '$file' not found.");
			}

			$info = pathinfo($path);
			$result = $builder->execute(array(
				$this->executable,
				'-ru',
				'--clean-css', // TODO: option??
				'--no-color',
				$path,
				$info['dirname'] . '/' . $info['filename'] . '.css',
			));

			$builder->logDebug($result->toText());

			if ($result->getCode() !== 0 || !is_file($path)) {
				throw new InvalidStateException("Compile LESS for file $file failed.");
			}
		}
	}
