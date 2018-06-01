<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Bind extends SimpleObject implements IBind {
		/** @var IParam */
		protected $param;
		/** @var mixed */
		protected $bind;

		/**
		 * @param IParam $param
		 * @param mixed  $bind
		 */
		public function __construct(IParam $param, $bind) {
			$this->param = $param;
			$this->bind = $bind;
		}

		/** @inheritdoc */
		public function getParam(): IParam {
			return $this->param;
		}

		/** @inheritdoc */
		public function getBind() {
			return $this->bind;
		}
	}
