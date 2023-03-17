<?php

	namespace Deliverist\Builder\Commands;

	use Deliverist\Builder\Builder;
	use Deliverist\Builder\CommandException;
	use Deliverist\Builder\ICommand;


	class Rename implements ICommand
	{
		/**
		 * @param  string|string[] $from
		 * @param  string|NULL $to
		 * @throws RenameException
		 */
		public function run(Builder $builder, $from = NULL, $to = NULL)
		{
			if (!isset($from)) {
				throw new RenameException("Missing parameter 'from'.");
			}

			$paths = [];

			if (is_array($from)) {
				$paths = $from;

			} else {
				if ($to === NULL) {
					throw new RenameException("Missing parameter 'to'.");
				}

				$paths[$from] = $to;
			}

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
