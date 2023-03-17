<?php

	namespace Deliverist\Builder;


	interface Command
	{
		/**
		 * @return void
		 */
		function run(Builder $builder/* , ...args */);
	}
