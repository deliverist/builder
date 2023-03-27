<?php

	namespace Deliverist\Builder\Loggers;


	class TextLogger implements \Deliverist\Builder\Logger
	{
		public function logCommandStart($commandName, array $parameters)
		{
			echo '[START] ', $commandName, "\n";
		}


		public function logCommandEnd($commandName, $elapsedTime)
		{
			echo '[END] ', $commandName, "\n";
		}


		public function logDebug($msg)
		{
			echo '[DEBUG] ', $msg, "\n";
		}


		public function logInfo($msg)
		{
			echo '[INFO] ', $msg, "\n";
		}


		public function logWarning($msg)
		{
			echo '[WARNING] ', $msg, "\n";
		}


		public function logError($msg)
		{
			echo '[ERROR] ', $msg, "\n";
		}


		public function logSuccess($msg)
		{
			echo '[SUCCESS] ', $msg, "\n";
		}
	}
