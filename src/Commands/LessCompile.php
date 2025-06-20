<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\FileSystemException;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\InvalidStateException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;
	use Nette\Utils\FileSystem;


	class LessCompile implements Command
	{
		/** @var string */
		private $executable;


		/**
		 * @param  string $executable
		 */
		public function __construct($executable = 'lessc')
		{
			$this->executable = $executable;
		}


		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'files')) {
				foreach (Parameters::stringList($params, 'files') as $file) {
					$this->processFile($builder, $file);
				}

			} else {
				$this->processFile($builder, Parameters::string($params, 'file'));
			}
		}


		/**
		 * @param  string $file
		 * @return void
		 */
		public function processFile(Builder $builder, $file)
		{
			$path = $builder->getPath($file);

			if (!is_file($path)) {
				throw new FileSystemException("File '$file' not found.");
			}

			$info = pathinfo($path);
			$newPath = (isset($info['dirname']) ? $info['dirname'] : '') . '/' . $info['filename'] . '.css';
			$result = $builder->execute([
				$this->executable,
				'-ru',
				'--clean-css', // TODO: option??
				'--no-color',
				$path,
				$newPath,
			]);

			$builder->logDebug($result->toText());

			if ($result->getCode() !== 0 || !is_file($newPath)) {
				throw new InvalidStateException("Compile LESS for file $file failed.");
			}
		}
	}
