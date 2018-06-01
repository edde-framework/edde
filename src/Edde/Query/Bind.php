<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_key_exists;
	use function array_keys;
	use function implode;
	use function sprintf;

	class Bind extends SimpleObject implements IBind {
		/** @var IParams */
		protected $params;
		/** @var array */
		protected $bind;

		/**
		 * @param IParams $params
		 * @param array   $bind
		 */
		public function __construct(IParams $params, array $bind) {
			$this->params = $params;
			$this->bind = $bind;
		}

		/** @inheritdoc */
		public function getParam(string $name): IParam {
			$param = $this->params->getParam($name);
			if (isset($this->bind[$name]) === false && array_key_exists($name, $this->bind) === false) {
				throw new QueryException(sprintf('Parameter [%s] exists, but it is not bound. Bound parameters are [%s].', $name, implode(', ', array_keys($this->bind))));
			}
			return $param;
		}

		/** @inheritdoc */
		public function getBind(string $name) {
			/**
			 * this call is here to use getParam() with it's internal check of param existence
			 */
			return $this->bind[$this->getParam($name)->getName()];
		}
	}
