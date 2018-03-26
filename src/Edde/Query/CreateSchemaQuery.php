<?php
	declare(strict_types=1);
	namespace Edde\Query;

	class CreateSchemaQuery extends AbstractQuery {
		/** @var string */
		protected $schema;

		public function __construct(string $schema) {
			$this->schema = $schema;
		}

		/** @inheritdoc */
		public function getSchema(): string {
			return $this->schema;
		}
	}
