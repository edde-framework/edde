<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use IteratorAggregate;
	use Traversable;

	interface IParams extends IteratorAggregate {
		/**
		 * @param IParam $param
		 *
		 * @return IParams
		 */
		public function param(IParam $param): IParams;

		/**
		 * @param string $name
		 *
		 * @return IParam
		 *
		 * @throws QueryException
		 */
		public function getParam(string $name): IParam;

		/**
		 * @return Traversable|Param[]
		 */
		public function getIterator();
	}