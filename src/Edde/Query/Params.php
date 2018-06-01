<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_key_exists;
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
		public function params(array $values): array {
			$params = [];
			foreach ($this->params as $name => $param) {
				if (array_key_exists($name, $values) === false) {
					throw new QueryException(sprintf('Missing parameter [%s] in values.', $name));
				}
				$params[$name] = $param->setValue($values[$name]);
			}
			return $params;
		}
	}
