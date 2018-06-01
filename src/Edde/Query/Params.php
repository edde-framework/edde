<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_keys;
	use function implode;
	use function sprintf;

	class Params extends SimpleObject implements IParams {
		/** @var IParam[] */
		protected $params = [];

		/** @inheritdoc */
		public function param(IParam $param): IParams {
			$this->params[$param->getName()] = $param;
			return $this;
		}

		/** @inheritdoc */
		public function getParam(string $name): IParam {
			if (isset($this->params[$name]) === false) {
				throw new QueryException(sprintf('Requested unknown parameter [%s]; available parameters are [%s].', $name, implode(', ', array_keys($this->params))));
			}
			return $this->params[$name];
		}

		/** @inheritdoc */
		public function getBinds(array $binds): array {
			$array = [];
			foreach ($binds as $k => &$v) {
				$array[$k] = new Bind($this->getParam($k), $v);
			}
			return $array;
		}
	}
