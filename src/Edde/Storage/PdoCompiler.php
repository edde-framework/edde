<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\IQuery;
	use Edde\Query\IWhere;
	use Edde\Query\QueryException;
	use function implode;
	use function strtoupper;
	use function vsprintf;

	class PdoCompiler extends AbstractCompiler {
		/** @inheritdoc */
		public function compile(IQuery $query): string {
			$isCount = $query->isCount();
			$columns = [];
			$from = [];
			foreach (($selects = $query->getSelects()) as $alias => $schema) {
				foreach ($schema->getAttributes() as $name => $attribute) {
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
				if ($query->isAttached($alias)) {
					continue;
				}
				$from[] = vsprintf('%s %s', [
					$this->delimit($schema->getRealName()),
					$this->delimit($alias),
				]);
			}
			foreach ($query->getAttaches() as $attach) {
				($relationSchema = $query->getSchema($attach->relation))->checkRelation(
					$sourceSchema = $query->getSchema($attach->attach),
					$targetSchema = $query->getSchema($attach->to)
				);
				$from[] = vsprintf("%s %s\n\t\tINNER JOIN %s %s ON %2\$s.%s = %4\$s.%s\n\t\tINNER JOIN %s %s ON %2\$s.%s = %8\$s.%s", [
					$this->delimit($relationSchema->getRealName()),
					$this->delimit($attach->relation),
					$this->delimit($sourceSchema->getRealName()),
					$this->delimit($attach->attach),
					$this->delimit($relationSchema->getSource()->getName()),
					$this->delimit($sourceSchema->getPrimary()->getName()),
					$this->delimit($targetSchema->getRealName()),
					$this->delimit($attach->to),
					$this->delimit($relationSchema->getTarget()->getName()),
					$this->delimit($targetSchema->getPrimary()->getName()),
				]);
			}
			$sql = vsprintf("SELECT\n\t%s\nFROM\n\t%s\n", [
				implode(",\n\t", $columns),
				implode(",\n\t", $from),
			]);
			if (($chains = ($wheres = $query->wheres())->chains())->hasChains()) {
				$sql .= vsprintf("WHERE\n\t%s\n", [
					$this->chain($wheres, $chains, $chains->getChain()),
				]);
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
		 * @param IWhere $where
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function where(IWhere $where): string {
			switch (($stdClass = $where->toObject())->type) {
				case 'equalTo':
					return vsprintf('%s.%s = :%s', [
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
				default:
					throw new QueryException(sprintf('Unsupported where type [%s] in [%s].', $stdClass->type, static::class));
			}
		}
	}
