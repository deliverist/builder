<?php

	namespace Deliverist\Builder;

	use CzProject\PathHelper;
	use CzProject\Runner;
	use Nette\Utils\Callback;
	use Nette\Utils\FileSystem;


	class Builder
	{
		const DEBUG = 0;
		const INFO = 1;
		const SUCCESS = 2;
		const WARNING = 3;
		const ERROR = 4;

		const MAKE_START = 0;
		const MAKE_END = 1;

		/** @var callback[]  (message, type) */
		public $onLog;

		/** @var callback[]  (command name, type self::MAKE_*) */
		public $onMake;

		/** @var string */
		private $directory;

		/** @var array  [name => command] */
		private $commands;

		/** @var Runner\Runner */
		protected $runner;


		/**
		 * @param  string
		 * @param  ICommand[]
		 */
		public function __construct($directory, array $commands = array())
		{
			$this->directory = PathHelper::absolutizePath($directory);
			$this->commands = $commands;
			$this->runner = $this->createRunner($this->directory);
		}


		/**
		 * @param  string|NULL
		 * @return string
		 */
		public function getPath($path = NULL)
		{
			return PathHelper::absolutizePath($this->directory . '/' . $path);
		}


		/**
		 * @param  string|ICommand|callback
		 * @return self
		 * @throws BuilderException
		 */
		public function make($command/*, ...*/)
		{
			$cmd = $command;
			$commandName = 'callback';

			if (is_string($command)) {
				if (!isset($this->commands[$command])) {
					throw new BuilderException("Missing command '$command'.");
				}

				$commandName = $command;
				$cmd = $this->commands[$command];
			}

			$args = func_get_args();
			array_shift($args);
			array_unshift($args, $this);

			$this->fireEvent($this->onMake, array($commandName, self::MAKE_START));

			if ($cmd instanceof ICommand) {
				Callback::invokeArgs(array($cmd, 'run'), $args);

			} else {
				Callback::invokeArgs($cmd, $args);
			}

			$this->fireEvent($this->onMake, array($commandName, self::MAKE_END));

			return $this;
		}


		/**
		 * @param  string|array
		 * @param  string|NULL
		 * @return Runner\RunnerResult
		 */
		public function execute($cmd, $subdir = NULL)
		{
			return $this->runner->run($cmd, $subdir);
		}


		/**
		 * @param  string
		 * @return string
		 */
		public function readFile($file)
		{
			return FileSystem::read($this->getPath($file));
		}


		/**
		 * @param  string
		 * @param  string
		 * @return void
		 */
		public function writeFile($file, $content)
		{
			FileSystem::write($this->getPath($file), $content);
		}


		/**
		 * @param  string
		 * @return void
		 */
		public function delete($path)
		{
			FileSystem::delete($this->getPath($path));
		}


		/**
		 * @param  string
		 * @param  int
		 * @return self
		 */
		public function log($message, $type = self::INFO)
		{
			$this->fireEvent($this->onLog, array($message, $type));
			return $this;
		}


		/**
		 * @param  string
		 * @return self
		 */
		public function logDebug($message)
		{
			return $this->log($message, self::DEBUG);
		}


		/**
		 * @param  string
		 * @return self
		 */
		public function logWarning($message)
		{
			return $this->log($message, self::WARNING);
		}


		/**
		 * @param  string
		 * @return self
		 */
		public function logError($message)
		{
			return $this->log($message, self::ERROR);
		}


		/**
		 * @param  string
		 * @return self
		 */
		public function logSuccess($message)
		{
			return $this->log($message, self::SUCCESS);
		}


		/**
		 * @param  string
		 * @return Runner\Runner
		 */
		protected function createRunner($directory)
		{
			return new Runner\Runner($directory);
		}


		/**
		 * @param  array|NULL
		 * @param  array
		 * @return void
		 */
		private function fireEvent($handlers, array $args = array())
		{
			if (!is_array($handlers)) {
				return;
			}

			foreach ($handlers as $handler) {
				Callback::invokeArgs($handler, $args);
			}
		}
	}
