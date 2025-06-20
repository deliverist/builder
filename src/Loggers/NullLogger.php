<?php

	declare(strict_types=1);

	namespace Deliverist\Builder\Loggers;


	class NullLogger implements \Deliverist\Builder\Logger
	{
		public function logCommandStart($commandName, array $parameters)
		{
		}


		public function logCommandEnd($commandName, $elapsedTime)
		{
		}


		public function logDebug($msg)
		{
		}


		public function logInfo($msg)
		{
		}


		public function logWarning($msg)
		{
		}


		public function logError($msg)
		{
		}


		public function logSuccess($msg)
		{
		}
	}
