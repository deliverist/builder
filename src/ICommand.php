<?php

	namespace Deliverist\Builder;


	interface ICommand
	{
		/**
		 * @return void
		 */
		function run(Builder $builder/* , ...args */);
	}
