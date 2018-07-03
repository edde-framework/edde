<?php
	declare(strict_types = 1);

	namespace Foo\Bar;

	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;

	interface IHeader {
	}

	class HeaderSchema extends Schema {
		public function __construct() {
			parent::__construct(Header::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->unique()
					->identifier()
					->required(),
				new SchemaProperty($this, 'name'),
			]);
		}
	}

	class RowSchema extends Schema {
		/**
		 * @var HeaderSchema
		 */
		protected $headerSchema;
		/**
		 * @var ItemSchema
		 */
		protected $itemSchema;

		/**
		 * @param HeaderSchema $headerSchema
		 * @param ItemSchema $itemSchema
		 */
		public function __construct(HeaderSchema $headerSchema, ItemSchema $itemSchema) {
			parent::__construct(Row::class);
			$this->headerSchema = $headerSchema;
			$this->itemSchema = $itemSchema;
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->unique()
					->identifier()
					->required(),
				$rowHeaderProperty = (new SchemaProperty($this, 'header'))->required(),
				$rowItemProperty = new SchemaProperty($this, 'item'),
				new SchemaProperty($this, 'name'),
				new SchemaProperty($this, 'value'),
			]);
			$this->linkTo('header', 'rowCollection', $rowHeaderProperty, $this->headerSchema->getProperty('guid'));
			$this->linkTo('item', 'rowItemCollection', $rowItemProperty, $this->itemSchema->getProperty('guid'));
		}
	}

	class ItemSchema extends Schema {
		public function __construct() {
			parent::__construct(Item::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->identifier()
					->unique()
					->required(),
				new SchemaProperty($this, 'name'),
			]);
		}
	}

	class Header2Schema extends Schema {
		public function __construct() {
			parent::__construct(Header2::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->unique()
					->identifier()
					->required(),
				new SchemaProperty($this, 'name'),
			]);
			$this->setMetaList([
				'implements' => [IHeader::class],
			]);
		}
	}

	class Row2Schema extends Schema {
		/**
		 * @var HeaderSchema
		 */
		protected $headerSchema;
		/**
		 * @var ItemSchema
		 */
		protected $itemSchema;

		/**
		 * @param Header2Schema $headerSchema
		 * @param Item2Schema $itemSchema
		 */
		public function __construct(Header2Schema $headerSchema, Item2Schema $itemSchema) {
			parent::__construct(Row2::class);
			$this->headerSchema = $headerSchema;
			$this->itemSchema = $itemSchema;
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->unique()
					->identifier()
					->required(),
				$rowHeaderProperty = (new SchemaProperty($this, 'header'))->required(),
				$rowItemProperty = new SchemaProperty($this, 'item'),
				new SchemaProperty($this, 'name'),
				new SchemaProperty($this, 'value'),
			]);
			$this->linkTo('header', 'rowCollection', $rowHeaderProperty, $this->headerSchema->getProperty('guid'));
			$this->linkTo('item', 'rowItemCollection', $rowItemProperty, $this->itemSchema->getProperty('guid'));
		}
	}

	class Item2Schema extends Schema {
		public function __construct() {
			parent::__construct(Item2::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->identifier()
					->unique()
					->required(),
				new SchemaProperty($this, 'name'),
				new SchemaProperty($this, 'things', 'string', false, false, false, true),
			]);
		}
	}

