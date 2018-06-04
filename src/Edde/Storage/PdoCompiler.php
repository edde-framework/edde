<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\IQuery;
	use Edde\Query\IWhere;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;
	use function implode;
	use function is_object;
	use function strtoupper;
	use function vsprintf;

	class PdoCompiler extends AbstractCompiler {
		/** @inheritdoc */
		public function compile(IQuery $query): string {
			$isCount = $query->isCount();
			$columns = [];
			$from = [];
			foreach ($query->getReturns() as $alias) {
				if (is_object($alias)) {
					$columns[] = $isCount ?
						vsprintf('COUNT(%s.%s) AS %s', [
							$this->delimit($alias->alias),
							$this->delimit($alias->property),
							$this->delimit($alias->name),
						]) :
						vsprintf('%s.%s AS %s', [
							$this->delimit($alias->alias),
							$this->delimit($alias->property),
							$this->delimit($alias->name),
						]);
					continue;
				}
				foreach ($query->getSchema($alias)->getAttributes() as $name => $attribute) {
					$columns[] = $isCount ?
						vsprintf('COUNT(%s.%s) AS %s', [
							$this->delimit($alias),
							$this->delimit($name),
							$this->delimit($alias),
						]) :
						vsprintf('%s.%s AS %s', [
							$this->delimit($alias),
							$this->delimit($name),
							$this->delimit($alias . '.' . $name),
						]);
				}
			}
			foreach (($selects = $query->getSelects()) as $alias => $schema) {
				$from[$alias] = vsprintf('%s %s', [
					$this->delimit($schema->getRealName()),
					$this->delimit($alias),
				]);
			}
			$sql = vsprintf("SELECT\n\t%s\nFROM\n\t%s\n", [
				implode(",\n\t", $columns),
				implode(",\n\t", $from),
			]);
			if (($chains = ($wheres = $query->wheres())->chains())->hasChains()) {
				$sql .= vsprintf("WHERE\n\t(\n\t\t%s\n\t)", [
					$this->chain($query, $wheres, $chains, $chains->getChain()),
				]);
			}
			if ($query->hasAttaches()) {
				$fragment = " AND ";
				if ($chains->hasChains() === false) {
					$sql .= "WHERE\n\t";
					$fragment = null;
				}
				$sql .= $fragment . "(\n\t\t";
				$wheres = [];
				foreach ($query->getAttaches() as $attach) {
					($relationSchema = $query->getSchema($attach->relation))->checkRelation(
						$sourceSchema = $query->getSchema($attach->attach),
						$targetSchema = $query->getSchema($attach->to)
					);
					$wheres[] = vsprintf('%s.%s = %s.%s', [
						$this->delimit($attach->relation),
						$this->delimit($relationSchema->getSource()->getName()),
						$this->delimit($attach->attach),
						$this->delimit($sourceSchema->getPrimary()->getName()),
					]);
					$wheres[] = vsprintf('%s.%s = %s.%s', [
						$this->delimit($attach->relation),
						$this->delimit($relationSchema->getTarget()->getName()),
						$this->delimit($attach->to),
						$this->delimit($targetSchema->getPrimary()->getName()),
					]);
				}
				$sql .= implode(" AND\n\t\t", $wheres);
				$sql .= "\n\t)\n";
			}
			if ($isCount === false && $query->hasOrder() && $orders = $query->getOrders()) {
				$sql .= "ORDER BY\n\t";
				$orderList = [];
				foreach ($orders as $stdClass) {
					$orderList[] = vsprintf('%s.%s %s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						in_array($order = strtoupper($stdClass->order), ['ASC', 'DESC']) ? $order : 'ASC',
					]);
				}
				$sql .= implode(" ,\n\t", $orderList) . "\n";
			}
			if ($isCount === false && $query->hasPage() && $page = $query->getPage()) {
				$sql .= vsprintf("LIMIT\n\t%d\nOFFSET\n\t%d\n", [
					$page->size,
					$page->page * $page->size,
				]);
			}
			return $sql;
		}

		/**
		 * @param IQuery $query
		 * @param IWhere $where
		 *
		 * @return string
		 *
		 * @throws QueryException
		 * @throws SchemaException
		 */
		public function where(IQuery $query, IWhere $where): string {
			switch (($stdClass = $where->toObject())->type) {
				case 'equalTo':
					return vsprintf('%s.%s = :%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'lesserThan':
					return vsprintf('%s.%s < :%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'lesserThanEqual':
					return vsprintf('%s.%s <= :%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'greaterThan':
					return vsprintf('%s.%s > :%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$stdClass->param,
					]);
				case 'greaterThanEqual':
					return vsprintf('%s.%s >= :%s', [
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
					return vsprintf('%s.%s IN (SELECT item FROM %s)', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$this->delimit($stdClass->param),
					]);
				case 'notIn':
					return vsprintf('%s.%s NOT IN (SELECT item FROM %s)', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$this->delimit($stdClass->param),
					]);
				case 'notInQuery':
					return vsprintf("%s.%s NOT IN (\n%s\n)", [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$this->compile($query->getQuery($stdClass->query)),
					]);
				case 'literal':
					return (string)$stdClass->literal;
				default:
					throw new QueryException(sprintf('Unsupported where type [%s] in [%s].', $stdClass->type, static::class));
			}
		}
	}
