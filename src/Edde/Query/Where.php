<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use stdClass;

	class Where extends SimpleObject implements IWhere {
		/** @var string */
		protected $name;
		/** @var IParams */
		protected $params;
		/** @var stdClass */
		protected $where;

		/**
		 * @param string  $name
		 * @param IParams $params
		 */
		public function __construct(string $name, IParams $params) {
			$this->name = $name;
			$this->params = $params;
		}

		/** @inheritdoc */
		public function getParams(): IParams {
			return $this->params;
		}

		/** @inheritdoc */
		public function equalTo(string $alias, string $property, string $param = null): IWhere {
			$param = new Param($alias, $property, $param ?: $this->name);
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param->getHash(),
			];
			$this->params->param($param);
			return $this;
		}

		/** @inheritdoc */
		public function lesserThan(string $alias, string $property, string $param = null): IWhere {
			$param = new Param($alias, $property, $param ?: $this->name);
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param->getHash(),
			];
			$this->params->param($param);
			return $this;
		}

		/** @inheritdoc */
		public function lesserThanEqual(string $alias, string $property, string $param = null): IWhere {
			$param = new Param($alias, $property, $param ?: $this->name);
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param->getHash(),
			];
			$this->params->param($param);
			return $this;
		}

		/** @inheritdoc */
		public function greaterThan(string $alias, string $property, string $param = null): IWhere {
			$param = new Param($alias, $property, $param ?: $this->name);
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param->getHash(),
			];
			$this->params->param($param);
			return $this;
		}

		/** @inheritdoc */
		public function greaterThanEqual(string $alias, string $property, string $param = null): IWhere {
			$param = new Param($alias, $property, $param ?: $this->name);
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param->getHash(),
			];
			$this->params->param($param);
			return $this;
		}

		/** @inheritdoc */
		public function isNull(string $alias, string $property): IWhere {
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
			];
			return $this;
		}

		/** @inheritdoc */
		public function isNotNull(string $alias, string $property): IWhere {
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
			];
			return $this;
		}

		/** @inheritdoc */
		public function in(string $alias, string $property, string $param = null): IWhere {
			$param = new Param($alias, $property, $param ?: $this->name);
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param->getHash(),
			];
			$this->params->param($param);
			return $this;
		}

		/** @inheritdoc */
		public function inQuery(string $alias, string $property, string $query = null): IWhere {
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'query'    => $query ?: $this->name,
			];
			return $this;
		}

		/** @inheritdoc */
		public function notIn(string $alias, string $property, string $param = null): IWhere {
			$param = new Param($alias, $property, $param ?: $this->name);
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'param'    => $param->getHash(),
			];
			$this->params->param($param);
			return $this;
		}

		/** @inheritdoc */
		public function notInQuery(string $alias, string $property, string $query = null): IWhere {
			$this->where = (object)[
				'type'     => __FUNCTION__,
				'alias'    => $alias,
				'property' => $property,
				'query'    => $query ?: $this->name,
			];
			return $this;
		}

		/** @inheritdoc */
		public function literal(string $literal): IWhere {
			$this->where = (object)[
				'type'    => __FUNCTION__,
				'literal' => $literal,
			];
			return $this;
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			return $this->where;
		}
	}
