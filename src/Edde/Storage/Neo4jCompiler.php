<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\IQuery;
	use Edde\Query\IWhere;
	use Edde\Query\QueryException;
	use function is_object;
	use function vsprintf;

	class Neo4jCompiler extends AbstractCompiler {
		public function __construct() {
			parent::__construct('`');
		}

		/** @inheritdoc */
		public function compile(IQuery $query): string {
			$isCount = $query->isCount();
			$from = [];
			foreach ($query->getSelects() as $alias => $schema) {
				if ($query->isAttached($alias)) {
					continue;
				}
				if ($schema->isRelation()) {
					$from[] = vsprintf('()-[%s:%s]->()', [
						$this->delimit($alias),
						$this->delimit($schema->getRealName()),
					]);
					continue;
				}
				$from[] = vsprintf('(%s:%s)', [
					$this->delimit($alias),
					$this->delimit($schema->getRealName()),
				]);
			}
			foreach ($query->getAttaches() as $attach) {
				($relationSchema = $query->getSchema($attach->relation))->checkRelation(
					$sourceSchema = $query->getSchema($attach->attach),
					$targetSchema = $query->getSchema($attach->to)
				);
				$from[] = vsprintf('(%s:%s)-[%s:%s]->(%s:%s)', [
					$this->delimit($attach->attach),
					$this->delimit($sourceSchema->getRealName()),
					$this->delimit($attach->relation),
					$this->delimit($relationSchema->getRealName()),
					$this->delimit($attach->to),
					$this->delimit($targetSchema->getRealName()),
				]);
			}
			$cypher = vsprintf("MATCH\n\t%s\n", [
				implode(",\n\t", $from),
			]);
			if (($chains = ($wheres = $query->wheres())->chains())->hasChains()) {
				$cypher .= vsprintf("WHERE\n\t%s\n", [
					$this->chain($wheres, $chains, $chains->getChain()),
				]);
			}
			$returns = [];
			foreach ($query->getReturns() as $return) {
				if (is_object($return)) {
					$returns[] = vsprintf($isCount ? 'COUNT(%s.%s) AS %s' : '%s.%s AS %s', [
						$this->delimit($return->alias),
						$this->delimit($return->property),
						$this->delimit($return->name),
					]);
					continue;
				}
				$return = $this->delimit($return);
				$returns[] = $isCount ?
					vsprintf('COUNT(%s) AS %s', [
						$return,
						$return,
					]) :
					$return;
			}
			$cypher .= "RETURN\n\t" . implode(',', $returns);
			if ($isCount === false && $query->hasOrder() && $orders = $query->getOrders()) {
				$cypher .= "\nORDER BY\n\t";
				$orderList = [];
				foreach ($orders as $stdClass) {
					$orderList[] = vsprintf('%s.%s %s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						in_array($order = strtoupper($stdClass->order), ['ASC', 'DESC']) ? $order : 'ASC',
					]);
				}
				$cypher .= implode(" ,\n\t", $orderList) . "\n";
			}
			if ($isCount === false && $query->hasPage() && $page = $query->getPage()) {
				$cypher .= vsprintf('SKIP %d LIMIT %d', [
					$page->page * $page->size,
					$page->size,
				]);
			}
			return $cypher;
		}

		/**
		 * @param IWhere $where
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function where(IWhere $where): string {
			switch (($stdClass = $where->toObject())->type) {
				case 'equalTo':
					return vsprintf('%s.%s = $%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'lesserThan':
					return vsprintf('%s.%s < $%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'lesserThanEqual':
					return vsprintf('%s.%s <= $%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'greaterThan':
					return vsprintf('%s.%s > $%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'greaterThanEqual':
					return vsprintf('%s.%s >= $%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'isNull':
					return vsprintf('%s.%s IS NULL', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
					]);
				case 'isNotNull':
					return vsprintf('%s.%s IS NOT NULL', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
					]);
				case 'in':
					return vsprintf('%s.%s IN ($%s)', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'notIn':
					return vsprintf('NOT %s.%s IN ($%s)', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				default:
					throw new QueryException(sprintf('Unsupported where type [%s] in [%s].', $stdClass->type, static::class));
			}
		}
	}
