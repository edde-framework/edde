<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\Command;
	use Edde\Query\Commands;
	use Edde\Query\IChain;
	use Edde\Query\IChains;
	use Edde\Query\ICommand;
	use Edde\Query\ICommands;
	use Edde\Query\IQuery;
	use Edde\Query\IWhere;
	use Edde\Query\IWheres;
	use Edde\Query\QueryException;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Service\Schema\SchemaManager;
	use function implode;
	use function sha1;
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
		public function compile(IQuery $query): ICommands {
			$commands = new Commands();
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
			$commands->addCommand(new Command($sql));
			return $commands;
		}

		/**
		 * @param IWheres $wheres
		 * @param IChains $chains
		 * @param IChain  $chain
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function chain(IWheres $wheres, IChains $chains, IChain $chain): string {
			foreach ($chain as $stdClass) {
				if ($chains->hasChain($stdClass->name)) {
					$this->chain($wheres, $chains, $chains->getChain($stdClass->name));
					continue;
				}
				$this->where($wheres->getWhere($stdClass->name));
			}
		}

		/**
		 * @param IWhere $where
		 * @param array  $params
		 *
		 * @return ICommand
		 *
		 * @throws QueryException
		 */
		public function where(IWhere $where, array $params): ICommand {
			$stdClass = $where->toObject();
			switch ($stdClass->type) {
				case 'equalTo':
					if (isset($params[$stdClass->param]) === false) {
						throw new QueryException(sprintf('Missing where parameter [%s]; available parameters [%s].', $stdClass->param, implode(', ', $params)));
					}
					$fragment = vsprintf('%s.%s = :%s', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						$paramId = $this->param($stdClass->param),
					]);
					$params[$paramId] = $this->filterValue($schemas[$selects[$stdClass->alias]]->getAttribute($stdClass->property), $params[$stdClass->param]);
					unset($params[$stdClass->param]);
					return $fragment;
				case 'in':
					if (isset($params[$stdClass->param]) === false) {
						throw new QueryException(sprintf('Missing where parameter [%s]; available parameters [%s].', $stdClass->param, implode(', ', $params)));
					} else if (is_iterable($params[$stdClass->param]) === false) {
						throw new QueryException(sprintf('Where in parameter [%s] is not an iterable.', $stdClass->param));
					}
					$in = [];
//					$schema = $schemas[$selects[$stdClass->alias]];
//					$attribute = $schema->getAttribute($stdClass->property);
					unset($params[$stdClass->param]);
					return vsprintf('%s.%s IN (:%s)', [
						$this->delimit($stdClass->alias),
						$this->delimit($stdClass->property),
						implode(', :', $in),
					]);
				default:
					throw new QueryException(sprintf('Unsupported where type [%s] in [%s].', $stdClass->type, static::class));
			}
		}

		protected function param(string $name): string {
			return '_' . sha1($name);
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
