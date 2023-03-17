<?php

	class TestBuilder extends Deliverist\Builder\Builder
	{
		/**
		 * @return \CzProject\Runner\RunnerResult|NULL
		 */
		public function getRunnerResult()
		{
			$runner = $this->runner;

			if (!($runner instanceof TestRunner)) {
				throw new \Deliverist\Builder\InvalidStateException('Requires TestRunner instance.');
			}

			return $runner->lastResult;
		}


		protected function createRunner($directory)
		{
			return new TestRunner($directory);
		}
	}


	class TestRunner extends CzProject\Runner\Runner
	{
		/** @var \CzProject\Runner\RunnerResult|NULL */
		public $lastResult = NULL;


		/**
		 * @param  string|string[] $command
		 * @param  string $subdirectory
		 * @return \CzProject\Runner\RunnerResult
		 */
		public function run($command, $subdirectory = NULL)
		{
			$cmd = is_string($command) ? $command : $this->processCommand($command);
			$result = new CzProject\Runner\RunnerResult($cmd, 0, ['Directory: ' . $subdirectory]);
			$this->lastResult = $result;
			return $result;
		}
	}
