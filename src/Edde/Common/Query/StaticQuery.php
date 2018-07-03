<?php
	declare(strict_types=1);

	namespace Edde\Common\Query;

	use Edde\Api\Query\IStaticQuery;
	use Edde\Common\Object;

	class StaticQuery extends Object implements IStaticQuery {
		/**
		 * @var mixed
		 */
		protected $query;
		/**
		 * @var array
		 */
		protected $parameterList;

		/**
		 * @param mixed $query
		 * @param array $parameterList
		 */
		public function __construct($query, array $parameterList = []) {
			$this->query = $query;
			$this->parameterList = $parameterList;
		}

		public function getQuery() {
			return $this->query;
		}

		public function hasParameterList() {
			return empty($this->parameterList) === false;
		}

		public function getParameterList() {
			return $this->parameterList;
		}
	}
