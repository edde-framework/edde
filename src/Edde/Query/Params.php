<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function sha1;
	use function sprintf;

	class Params extends SimpleObject implements IParams {
		/** @var IParam[] */
		protected $params = [];

		/** @inheritdoc */
		public function param(IParam $param): IParams {
			$this->params[$param->getHash()] = $param;
			return $this;
		}

		/** @inheritdoc */
		public function getParam(string $name): IParam {
			if (isset($this->params[$hash = sha1($name)]) === false) {
				throw new QueryException(sprintf('Requested unknown parameter [%s].', $name));
			}
			return $this->params[$hash];
		}

		/** @inheritdoc */
		public function binds(array $binds): IBinds {
			foreach ($binds as $k => &$v) {
				$v = new Bind($this->getParam($k), $v);
			}
			return new Binds($binds);
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->params;
		}
	}
