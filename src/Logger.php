<?php

	namespace Deliverist\Builder;


	interface Logger
	{
		/**
		 * @param  string $commandName
		 * @param  array<string, mixed> $parameters
		 * @return void
		 */
		function logCommandStart($commandName, array $parameters);


		/**
		 * @param  string $commandName
		 * @param  float $elapsedTime
		 * @return void
		 */
		function logCommandEnd($commandName, $elapsedTime);


		/**
		 * @param  string $msg
		 * @return void
		 */
		function logDebug($msg);


		/**
		 * @param  string $msg
		 * @return void
		 */
		function logInfo($msg);


		/**
		 * @param  string $msg
		 * @return void
		 */
		function logWarning($msg);


		/**
		 * @param  string $msg
		 * @return void
		 */
		function logError($msg);


		/**
		 * @param  string $msg
		 * @return void
		 */
		function logSuccess($msg);
	}
