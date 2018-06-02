<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Edde;
	use Edde\Query\IChain;
	use Edde\Query\IChains;
	use Edde\Query\IWhere;
	use Edde\Query\IWheres;
	use Edde\Service\Schema\SchemaManager;

	abstract class AbstractCompiler extends Edde implements ICompiler {
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
		public function delimit(string $delimit): string {
			return $this->delimiter . str_replace($this->delimiter, $this->delimiter . $this->delimiter, $delimit) . $this->delimiter;
		}

		public function chain(IWheres $wheres, IChains $chains, IChain $chain, int $level = 1): string {
			$fragments = [];
			$tabs = str_repeat("\t", $level);
			foreach ($chain as $stdClass) {
				$operator = ' ' . strtoupper($stdClass->operator) . ' ';
				if ($chains->hasChain($stdClass->name)) {
					$fragments[] = $operator;
					$fragments[] = "(\n\t" . $tabs . $this->chain($wheres, $chains, $chains->getChain($stdClass->name), $level + 1) . "\n" . $tabs . ')';
					continue;
				}
				$fragments[] = $operator . "\n" . $tabs;
				$fragments[] = $this->where($wheres->getWhere($stdClass->name));
			}
			/**
			 * shift the very first operator as it makes no sense
			 */
			array_shift($fragments);
			return implode('', $fragments);
		}

		abstract public function where(IWhere $where): string;
	}
