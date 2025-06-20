<?php

	declare(strict_types=1);

	namespace Deliverist\Builder;

	use CzProject\PathHelper;
	use CzProject\Runner;
	use Nette\Utils\Callback;
	use Nette\Utils\FileSystem;


	class Builder
	{
		/** @var string */
		private $directory;

		/** @var array<string, Command> */
		private $commands;

		/** @var Logger */
		private $logger;

		/** @var Runner\Runner */
		protected $runner;


		/**
		 * @param  string $directory
		 * @param  Command[] $commands
		 */
		public function __construct(
			$directory,
			array $commands,
			Logger $logger
		)
		{
			$this->directory = PathHelper::absolutizePath($directory);
			$this->commands = $commands;
			$this->runner = $this->createRunner($this->directory);
			$this->logger = $logger;
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
		 * @param  mixed ...$params
		 * @return self
		 * @throws BuilderException
		 */
		public function make($command, ...$params)
		{
			$commandName = '@anonymous';

			if (is_string($command)) {
				if (!isset($this->commands[$command])) {
					throw new BuilderException("Missing command '$command'.");
				}

				$commandName = $command;
				$command = $this->commands[$command];
			}

			$params = $this->filterParameters($params);

			$this->logger->logCommandStart($commandName, $params);
			$startedAt = microtime(TRUE);

			if ($command instanceof Command) {
				$command->run($this, $params);

			} else {
				call_user_func($command, $this, $params);
			}

			$this->logger->logCommandEnd($commandName, microtime(TRUE) - $startedAt);

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
		 * @return void
		 */
		public function log($message)
		{
			$this->logger->logInfo($message);
		}


		/**
		 * @param  string $message
		 * @return void
		 */
		public function logDebug($message)
		{
			$this->logger->logDebug($message);
		}


		/**
		 * @param  string $message
		 * @return void
		 */
		public function logWarning($message)
		{
			$this->logger->logWarning($message);
		}


		/**
		 * @param  string $message
		 * @return void
		 */
		public function logError($message)
		{
			$this->logger->logError($message);
		}


		/**
		 * @param  string $message
		 * @return void
		 */
		public function logSuccess($message)
		{
			$this->logger->logSuccess($message);
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
		 * @param  array<mixed> $params
		 * @return array<string, mixed>
		 */
		private function filterParameters(array $params)
		{
			if (count($params) === 1 && isset($params[0]) && is_array($params[0])) {
				$params = $params[0];
			}

			$res = [];

			foreach ($params as $paramName => $param) {
				if (!is_string($paramName)) {
					throw new InvalidArgumentException("Parameter name must be string, $paramName given.");
				}

				$res[$paramName] = $param;
			}

			return $params;
		}
	}
