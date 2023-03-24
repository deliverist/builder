<?php

	namespace Deliverist\Builder;


	class Parameters
	{
		public function __construct()
		{
			throw new \Deliverist\Builder\Exception('This is static class.');
		}


		/**
		 * @param  array<string, mixed|NULL> $args
		 * @param  string $name
		 * @return bool
		 */
		public static function has(array $args, $name)
		{
			return isset($args[$name]);
		}


		/**
		 * @param  array<string, mixed|NULL> $args
		 * @param  string $name
		 * @param  bool|NULL $default
		 * @return bool
		 */
		public static function bool(array $args, $name, $default = NULL)
		{
			$value = isset($args[$name]) ? $args[$name] : $default;

			if ($value === NULL) {
				throw new MissingParameterException("Missing parameter '$name'.");
			}

			if (is_bool($value)) {
				return $value;
			}

			throw new InvalidParameterTypeException("Invalid type of parameter '$name', requires bool, " . self::describeType($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed|NULL> $args
		 * @param  string $name
		 * @param  string|NULL $default
		 * @return string
		 */
		public static function string(array $args, $name, $default = NULL)
		{
			$value = isset($args[$name]) ? $args[$name] : $default;

			if ($value === NULL) {
				throw new MissingParameterException("Missing parameter '$name'.");
			}

			if (is_string($value)) {
				return $value;
			}

			throw new InvalidParameterTypeException("Invalid type of parameter '$name', requires string, " . self::describeType($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed|NULL> $args
		 * @param  string $name
		 * @return string|NULL
		 */
		public static function stringOrNull(array $args, $name)
		{
			if (!isset($args[$name])) {
				return NULL;
			}

			return self::string($args, $name);
		}


		/**
		 * @param  array<string, mixed|NULL> $args
		 * @param  string $name
		 * @param  string[]|NULL $default
		 * @return string[]
		 */
		public static function stringList(array $args, $name, array $default = NULL)
		{
			$value = isset($args[$name]) ? $args[$name] : $default;

			if ($value === NULL) {
				throw new MissingParameterException("Missing parameter '$name'.");
			}

			if (is_string($value)) {
				return [$value];

			} elseif (is_array($value)) {
				return array_values($value);
			}

			throw new InvalidParameterTypeException("Invalid type of parameter '$name', requires string[], " . self::describeType($value) . ' given.');
		}


		/**
		 * @param  array<string, mixed|NULL> $args
		 * @param  string $name
		 * @param  array<string, string>|NULL $default
		 * @return array<string, string>
		 */
		public static function stringMap(array $args, $name, array $default = NULL)
		{
			$value = isset($args[$name]) ? $args[$name] : $default;

			if ($value === NULL) {
				throw new MissingParameterException("Missing parameter '$name'.");
			}

			if (is_array($value)) {
				return $value;
			}

			throw new InvalidParameterTypeException("Invalid type of parameter '$name', requires array<string, string>, " . self::describeType($value) . ' given.');
		}


		/**
		 * @param  mixed $value
		 * @return string
		 */
		private static function describeType($value)
		{
			return is_object($value) ? get_class($value) : gettype($value);
		}
	}
