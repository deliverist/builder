<?php

	class TestBuilder extends Deliverist\Builder\Builder
	{
		public function getRunnerResult()
		{
			return $this->runner->lastResult;
		}


		protected function createRunner($directory)
		{
			return new TestRunner($directory);
		}
	}


	class TestRunner extends CzProject\Runner\Runner
	{
		public $lastResult;


		public function run($command, $subdirectory = NULL)
		{
			$cmd = is_string($command) ? $command : $this->processCommand($command);
			$result = new CzProject\Runner\RunnerResult($cmd, 0, array('Directory: ' . $subdirectory));
			$this->lastResult = $result;
			return $result;
		}
	}
