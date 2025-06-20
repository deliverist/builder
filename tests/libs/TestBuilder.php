<?php

	declare(strict_types=1);

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


	class TestLogger implements \Deliverist\Builder\Logger
	{
		/** @var string[] */
		private $log = [];


		/**
		 * @return string[]
		 */
		public function getLog()
		{
			return $this->log;
		}


		public function logCommandStart($commandName, array $parameters)
		{
			$this->log[] = '[START] ' . $commandName;
		}


		public function logCommandEnd($commandName, $elapsedTime)
		{
			$this->log[] = '[END] ' . $commandName;
		}


		public function logDebug($msg)
		{
			$this->log[] = '[DEBUG] ' . $msg;
		}


		public function logInfo($msg)
		{
			$this->log[] = '[INFO] ' . $msg;
		}


		public function logWarning($msg)
		{
			$this->log[] = '[WARNING] ' . $msg;
		}


		public function logError($msg)
		{
			$this->log[] = '[ERROR] ' . $msg;
		}


		public function logSuccess($msg)
		{
			$this->log[] = '[SUCCESS] ' . $msg;
		}
	}
