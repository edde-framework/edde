<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	class SimpleHydrator extends SchemaHydrator {
		/** @var string */
		protected $name;

		public function __construct(string $name, string $prefix = 'storage') {
			parent::__construct($prefix);
			$this->name = $name;
		}

		/** @inheritdoc */
		public function hydrate(array $source) {
			return $this->output($this->name, $source);
		}
	}
