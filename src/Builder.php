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

		/** @var callable[]  (message, type) */
		public $onLog;

		/** @var callable[]  (command name, type self::MAKE_*) */
		public $onMake;

		/** @var string */
		private $directory;

		/** @var array<string, Command> */
		private $commands;

		/** @var Runner\Runner */
		protected $runner;


		/**
		 * @param  string $directory
		 * @param  Command[] $commands
		 */
		public function __construct($directory, array $commands = [])
		{
			$this->directory = PathHelper::absolutizePath($directory);
			$this->commands = $commands;
			$this->runner = $this->createRunner($this->directory);
		}


		/**
		 * @param  string|NULL $path
		 * @return string
		 */
		public function getPath($path = NULL)
		{
			return PathHelper::absolutizePath($this->directory . '/' . $path);
		}


		/**
		 * @param  string|Command|callable $command
		 * @param  mixed ...$args
		 * @return self
		 * @throws BuilderException
		 */
		public function make($command, ...$args)
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

			array_unshift($args, $this);

			$this->fireEvent($this->onMake, [$commandName, self::MAKE_START]);

			if ($cmd instanceof Command) {
				Callback::invokeArgs([$cmd, 'run'], $args);

			} else {
				Callback::invokeArgs($cmd, $args);
			}

			$this->fireEvent($this->onMake, [$commandName, self::MAKE_END]);

			return $this;
		}


		/**
		 * @param  string|string[] $cmd
		 * @param  string|NULL $subdir
		 * @return Runner\RunnerResult
		 */
		public function execute($cmd, $subdir = NULL)
		{
			return $this->runner->run($cmd, $subdir);
		}


		/**
		 * @param  string $file
		 * @return string
		 */
		public function readFile($file)
		{
			return FileSystem::read($this->getPath($file));
		}


		/**
		 * @param  string $file
		 * @param  string $content
		 * @return void
		 */
		public function writeFile($file, $content)
		{
			FileSystem::write($this->getPath($file), $content);
		}


		/**
		 * @param  string $path
		 * @return void
		 */
		public function delete($path)
		{
			FileSystem::delete($this->getPath($path));
		}


		/**
		 * @param  string $message
		 * @param  int $type
		 * @return self
		 */
		public function log($message, $type = self::INFO)
		{
			$this->fireEvent($this->onLog, [$message, $type]);
			return $this;
		}


		/**
		 * @param  string $message
		 * @return self
		 */
		public function logDebug($message)
		{
			return $this->log($message, self::DEBUG);
		}


		/**
		 * @param  string $message
		 * @return self
		 */
		public function logWarning($message)
		{
			return $this->log($message, self::WARNING);
		}


		/**
		 * @param  string $message
		 * @return self
		 */
		public function logError($message)
		{
			return $this->log($message, self::ERROR);
		}


		/**
		 * @param  string $message
		 * @return self
		 */
		public function logSuccess($message)
		{
			return $this->log($message, self::SUCCESS);
		}


		/**
		 * @param  string $directory
		 * @return Runner\Runner
		 */
		protected function createRunner($directory)
		{
			return new Runner\Runner($directory);
		}


		/**
		 * @param  callable[]|NULL $handlers
		 * @param  array<mixed> $args
		 * @return void
		 */
		private function fireEvent($handlers, array $args = [])
		{
			if (!is_array($handlers)) {
				return;
			}

			foreach ($handlers as $handler) {
				Callback::invokeArgs($handler, $args);
			}
		}
	}
