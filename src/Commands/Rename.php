<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\CommandException;
	use Deliverist\Builder\Command;
	use Deliverist\Builder\Parameters;


	class Rename implements Command
	{
		public function run(Builder $builder, array $params)
		{
			if (Parameters::has($params, 'files')) {
				$this->process($builder, Parameters::stringMap($params, 'files'));

			} elseif (Parameters::has($params, 'paths')) {
				$this->process($builder, Parameters::stringMap($params, 'paths'));

			} else {
				$source = Parameters::string($params, 'from');
				$destination = Parameters::string($params, 'to');
				$this->process($builder, [
					$source => $destination,
				]);
			}
		}


		/**
		 * @param  array<string, string> $paths
		 * @return void
		 * @throws RenameException
		 */
		public function process(Builder $builder, array $paths)
		{
			foreach ($paths as $fromPath => $toPath) {
				$builder->log("Renaming '$fromPath' to '$toPath'.");
				$fromReal = $builder->getPath($fromPath);
				$toReal = $builder->getPath($toPath);

				@mkdir(dirname($toReal), 0777, TRUE); // @ - muze existovat
				$res = @rename($fromReal, $toReal);

				if ($res === FALSE) {
					throw new RenameException("Renaming of '$fromPath' to '$toPath' failed.");
				}
			}
		}
	}


	class RenameException extends CommandException
	{
	}
