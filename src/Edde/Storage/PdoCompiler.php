<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\Command;
	use Edde\Query\IBind;
	use Edde\Query\IChain;
	use Edde\Query\IChains;
	use Edde\Query\ICommand;
	use Edde\Query\IQuery;
	use Edde\Query\IWhere;
	use Edde\Query\IWheres;
	use Edde\Query\QueryException;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use function array_keys;
	use function array_shift;
	use function implode;
	use function is_iterable;
	use function sha1;
	use function str_repeat;
	use function strtoupper;
	use function vsprintf;

	class PdoCompiler extends AbstractCompiler {
		use SchemaManager;
		/** @var string */
		protected $delimiter;

		/**
		 * @param string $delimiter
		 */
		public function __construct(string $delimiter) {
			$this->delimiter = $delimiter;
		}

		/** @inheritdoc */
		public function compile(IQuery $query, IBind $bind = null): ICommand {
			$isCount = $query->isCount();
			$schemas = $this->getSchemas($query->getSchemas());
			$columns = [];
			$from = [];
			foreach (($selects = $query->getSelects()) as $alias => $schema) {
				foreach ($schemas[$schema]->getAttributes() as $name => $attribute) {
					$source = $schemas[$schema]->getRealName();
					$columns[] = $isCount ?
						vsprintf('COUNT(%s.%s) AS %s', [
							$this->delimit($source),
							$this->delimit($name),
							$this->delimit($source),
						]) :
						vsprintf('%s.%s AS %s', [
							$this->delimit($source),
							$this->delimit($name),
							$this->delimit($source . '.' . $name),
						]);
				}
				if ($query->isAttached($alias)) {
					continue;
				}
				$from[] = vsprintf('%s %s', [
					$this->delimit($schemas[$schema]->getRealName()),
					$this->delimit($alias),
				]);
			}
			foreach ($query->getAttaches() as $attach) {
				$sourceSchema = $schemas[$selects[$attach->attach]];
				$relationSchema = $schemas[$selects[$attach->relation]];
				$targetSchema = $schemas[$selects[$attach->to]];
				$this->checkRelation($relationSchema, $sourceSchema, $targetSchema);
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
					$this->chain($wheres, $chains, $chains->getChain(), $bind),
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
			return new Command($sql);
		}

		/**
		 * @param IWheres $wheres
		 * @param IChains $chains
		 * @param IChain  $chain
		 * @param IBind   $bind
		 * @param int     $level
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function chain(IWheres $wheres, IChains $chains, IChain $chain, IBind $bind, int $level = 1): string {
			$fragments = [];
			$tabs = str_repeat("\t", $level);
			foreach ($chain as $stdClass) {
				$operator = ' ' . strtoupper($stdClass->operator) . ' ';
				if ($chains->hasChain($stdClass->name)) {
					$fragments[] = $operator;
					$fragments[] = "(\n\t" . $tabs . $this->chain($wheres, $chains, $chains->getChain($stdClass->name), $bind, $level + 1) . "\n" . $tabs . ')';
					continue;
				}
				$fragments[] = $operator . "\n" . $tabs;
				$fragments[] = $this->where($wheres->getWhere($stdClass->name), $bind);
			}
			/**
			 * shift the very first operator as it makes no sense
			 */
			array_shift($fragments);
			return implode('', $fragments);
		}

		/**
		 * @param IWhere $where
		 * @param IBind  $bind
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function where(IWhere $where, IBind $bind): string {
			$stdClass = $where->toObject();
			switch ($stdClass->type) {
				case 'equalTo':
					return vsprintf('%s.%s = :%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$bind->getParam($stdClass->param)->getHash(),
					]);
				case 'in':
					if (is_iterable($value = $bind->getBind($stdClass->param)) === false) {
						throw new QueryException(sprintf('Parameter [%s] must be iterable (array, generator, ...).', $stdClass->param));
					}
					$in = [];
					foreach ($value as $k => $v) {
						$in['_' . sha1($k . $stdClass->param)] = $v;
					}
					return vsprintf('%s.%s IN (%s)', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						empty($in) ? 'SELECT TRUE LIMIT 0 OFFSET 0' : ':' . implode(', :', array_keys($in)),
					]);
				default:
					throw new QueryException(sprintf('Unsupported where type [%s] in [%s].', $stdClass->type, static::class));
			}
		}

		/** @inheritdoc */
		public function delimit(string $delimit): string {
			return $this->delimiter . str_replace($this->delimiter, $this->delimiter . $this->delimiter, $delimit) . $this->delimiter;
		}

		/**
		 * @param array $selects
		 *
		 * @return ISchema[]
		 *
		 * @throws SchemaException
		 */
		protected function getSchemas(array $selects): array {
			$schemas = [];
			foreach ($selects as $schema) {
				$schemas[$schema] = $this->schemaManager->getSchema($schema);
			}
			return $schemas;
		}
	}
