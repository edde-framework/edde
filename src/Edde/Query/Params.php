<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_keys;
	use function implode;

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
				throw new QueryException(sprintf('Requested unknown parameter [%s]; available parameters are [%s].', implode(', ', array_keys($this->params))));
			}
			return $this->params[$name];
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->params;
		}
	}
