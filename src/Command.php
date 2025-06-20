<?php

	declare(strict_types=1);

	namespace Deliverist\Builder;


	interface Command
	{
		/**
		 * @param  array<string, mixed> $params
		 * @return void
		 */
		function run(Builder $builder, array $params);
	}
