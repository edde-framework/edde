<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Edde;
	use stdClass;

	class Attribute extends Edde implements IAttribute {
		/** @var stdClass */
		protected $source;

		public function __construct(stdClass $source) {
			$this->source = $source;
		}

		/** @inheritdoc */
		public function getName(): string {
			return (string)$this->source->name;
		}

		/** @inheritdoc */
		public function getType(): string {
			return (string)($this->source->type ?? 'string');
		}

		/** @inheritdoc */
		public function isPrimary(): bool {
			return (bool)($this->source->primary ?? false);
		}

		/** @inheritdoc */
		public function isUnique(): bool {
			return (bool)($this->source->unique ?? false);
		}

		/** @inheritdoc */
		public function isRequired(): bool {
			return (bool)($this->source->required ?? false);
		}

		/** @inheritdoc */
		public function isLink(): bool {
			return (bool)($this->source->link ?? false);
		}

		/** @inheritdoc */
		public function getGenerator(): ?string {
			return isset($this->source->generator) ? (string)$this->source->generator : null;
		}

		/** @inheritdoc */
		public function getFilter(): ?string {
			return isset($this->source->filter) ? (string)$this->source->filter : null;
		}

		/** @inheritdoc */
		public function getSanitizer(): ?string {
			return isset($this->source->sanitizer) ? (string)$this->source->sanitizer : null;
		}

		/** @inheritdoc */
		public function getValidator(): ?string {
			return isset($this->source->validator) ? (string)$this->source->validator : null;
		}

		/** @inheritdoc */
		public function getDefault() {
			return isset($this->source->default) ? $this->source->default : null;
		}
	}
