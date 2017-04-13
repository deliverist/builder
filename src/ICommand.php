<?php

	namespace Deliverist\Builder;


	interface ICommand
	{
		/**
		 * @param  Builder
		 */
		function run(Builder $builder/* , ...args */);
	}
