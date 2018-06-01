<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IParam {
		/**
		 * @return string
		 */
		public function getName(): string;

		/**
		 * @return string
		 */
		public function getAlias(): string;

		/**
		 * @return string
		 */
		public function getProperty(): string;

		/**
		 * return param hash
		 *
		 * @return string
		 */
		public function getHash(): string;

		/**
		 * bind a value to a param
		 *
		 * @param mixed $value
		 *
		 * @return IParam
		 */
		public function setValue($value): IParam;

		/**
		 * @return mixed
		 */
		public function getValue();
	}
