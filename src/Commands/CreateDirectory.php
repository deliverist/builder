<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\InvalidArgumentException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;
	use Nette\Utils\FileSystem;


	class CreateDirectory implements Command
	{
		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'directories')) {
				$this->process($builder, Parameters::stringList($params, 'directories'));

			} else {
				$this->process($builder, [Parameters::string($params, 'directory')]);
			}
		}


		/**
		 * @param  string[] $directories
		 * @return void
		 */
		public function process(Builder $builder, array $directories)
		{
			foreach ($directories as $directory) {
				$builder->log("Create directory '$directory'.");
				FileSystem::createDir($builder->getPath($directory));
			}
		}
	}
