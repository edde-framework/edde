<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IBind {
		/**
		 * return bind param definition
		 *
		 * @return IParam
		 */
		public function getParam(): IParam;

		/**
		 * return actual bound value
		 *
		 * @return mixed
		 */
		public function getValue();
	}
