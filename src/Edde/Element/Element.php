<?php
	declare(strict_types=1);
	namespace Edde\Element;

	use Edde\Bus\BusException;
	use Edde\Edde;

	class Element extends Edde implements IElement {
		/** @var string */
		protected $type;
		/** @var string */
		protected $uuid;
		/** @var string */
		protected $target;
		/** @var string | null */
		protected $reference;
		/** @var IElement[] */
		protected $sends = [];
		/** @var IElement[] */
		protected $executes = [];
		/** @var IElement[] */
		protected $responses = [];
		/** @var array */
		protected $attributes;
		/** @var array */
		protected $metas;

		/** @inheritdoc */
		public function __construct(string $type, string $uuid, array $attributes = [], array $metas = []) {
			$this->type = $type;
			$this->uuid = $uuid;
			$this->target = 'local';
			$this->attributes = $attributes;
			$this->metas = $metas;
		}

		/** @inheritdoc */
		public function getVersion(): string {
			return (string)$this->getAttribute('version');
		}

		/** @inheritdoc */
		public function getType(): string {
			return $this->type;
		}

		/** @inheritdoc */
		public function getUuid(): ?string {
			return $this->uuid;
		}

		/** @inheritdoc */
		public function getTarget(): string {
			return $this->target;
		}

		/** @inheritdoc */
		public function setTarget(string $target): IElement {
			$this->target = $target;
			return $this;
		}

		/** @inheritdoc */
		public function setReference(string $reference): IElement {
			$this->reference = $reference;
			return $this;
		}

		/** @inheritdoc */
		public function hasReference(): bool {
			return $this->reference !== null;
		}

		/** @inheritdoc */
		public function getReference(): ?string {
			return $this->reference;
		}

		/** @inheritdoc */
		public function isAsync(): bool {
			return (bool)$this->getAttribute('async', false);
		}

		/** @inheritdoc */
		public function hasQueue(): bool {
			return $this->getAttribute('queue') !== null;
		}

		/** @inheritdoc */
		public function getQueue(): string {
			if ($this->hasQueue() === false) {
				throw new BusException(sprintf('Element [%s (%s)] does not have queue.', $this->getType(), static::class));
			}
			return (string)$this->getAttribute('queue');
		}

		/** @inheritdoc */
		public function setAttribute(string $name, $value): IElement {
			$this->attributes[$name] = $value;
			return $this;
		}

		/** @inheritdoc */
		public function setAttributes(array $attributes): IElement {
			$this->attributes = $attributes;
			return $this;
		}

		/** @inheritdoc */
		public function hasAttribute(string $name): bool {
			return isset($this->attributes[$name]);
		}

		/** @inheritdoc */
		public function getAttribute(string $name, $default = null) {
			return $this->attributes[$name] ?? $default;
		}

		/** @inheritdoc */
		public function getAttributes(): array {
			return $this->attributes;
		}

		/** @inheritdoc */
		public function setMeta(string $name, $value): IElement {
			$this->metas[$name] = $value;
			return $this;
		}

		/** @inheritdoc */
		public function setMetas(array $metas): IElement {
			$this->metas = $metas;
			return $this;
		}

		/** @inheritdoc */
		public function hasMeta(string $name): bool {
			return isset($this->metas[$name]);
		}

		/** @inheritdoc */
		public function getMeta(string $name, $default = null) {
			return $this->metas[$name] ?? $default;
		}

		/** @inheritdoc */
		public function getMetas(): array {
			return $this->metas;
		}

		/** @inheritdoc */
		public function send(IElement $element): IElement {
			$this->sends[$element->getUuid()] = $element;
			return $this;
		}

		/** @inheritdoc */
		public function getSends(): array {
			return $this->sends;
		}

		/** @inheritdoc */
		public function execute(IElement $element): IElement {
			$this->executes[$element->getUuid()] = $element;
			return $this;
		}

		/** @inheritdoc */
		public function getExecutes(): array {
			return $this->executes;
		}

		/** @inheritdoc */
		public function response(string $request, IElement $response): IElement {
			$this->responses[$request] = $response;
			return $this;
		}

		/** @inheritdoc */
		public function getResponses(): array {
			return $this->responses;
		}
	}
