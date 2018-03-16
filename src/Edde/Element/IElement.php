<?php
	declare(strict_types=1);
	namespace Edde\Element;

	use Edde\Bus\BusException;

	/**
	 * Base element for all message bus related stuff.
	 */
	interface IElement {
		/**
		 * get an element version
		 *
		 * @return string
		 */
		public function getVersion(): string;

		/**
		 * return type of an element (event, request, response, error, ...)
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * return uuid string (generally unique identifier)
		 *
		 * @return string
		 */
		public function getUuid(): ?string;

		/**
		 * get a target node for this element
		 *
		 * @return string
		 */
		public function getTarget(): string;

		/**
		 * set element's target
		 *
		 * @param string $target
		 *
		 * @return IElement
		 */
		public function setTarget(string $target): IElement;

		/**
		 * @param string $reference
		 *
		 * @return IElement
		 */
		public function setReference(string $reference): IElement;

		/**
		 * @return bool
		 */
		public function hasReference(): bool;

		/**
		 * @return string|null
		 */
		public function getReference(): ?string;

		/**
		 * is an element async?
		 *
		 * @return bool
		 */
		public function isAsync(): bool;

		/**
		 * has element queue? (thus should be enqueued?)
		 *
		 * @return bool
		 */
		public function hasQueue(): bool;

		/**
		 * return a queue name (if an element has one)
		 *
		 * @return string
		 *
		 * @throws BusException when an element does not have a queue
		 */
		public function getQueue(): string;

		/**
		 * set an attribute
		 *
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return IElement
		 */
		public function setAttribute(string $name, $value): IElement;

		/**
		 * override all attributes at once
		 *
		 * @param array $attributes
		 *
		 * @return IElement
		 */
		public function setAttributes(array $attributes): IElement;

		/**
		 * has the given value (checked by isset)?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasAttribute(string $name): bool;

		/**
		 * return an attribute of the given name
		 *
		 * @param string     $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getAttribute(string $name, $default = null);

		/**
		 * @return array
		 */
		public function getAttributes(): array;

		/**
		 * set a meta value
		 *
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return IElement
		 */
		public function setMeta(string $name, $value): IElement;

		/**
		 * set all meta data at once (override existing)
		 *
		 * @param array $metas
		 *
		 * @return IElement
		 */
		public function setMetas(array $metas): IElement;

		/**
		 * has the given meta value (checked by isset)?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasMeta(string $name): bool;

		/**
		 * return a meta value of the given name
		 *
		 * @param string     $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getMeta(string $name, $default = null);

		/**
		 * @return array
		 */
		public function getMetas(): array;

		/**
		 * the given element should be processed in common way (MessageBus::send()) on the
		 * other side
		 *
		 * @param IElement $element
		 *
		 * @return IElement
		 */
		public function send(IElement $element): IElement;

		/**
		 * @return IElement[]
		 */
		public function getSends(): array;

		/**
		 * the given element should be executed on the other side
		 *
		 * @param IElement $element
		 *
		 * @return IElement
		 */
		public function execute(IElement $element): IElement;

		/**
		 * @return IElement[]
		 */
		public function getExecutes(): array;

		/**
		 * set response to the given message ($request should not be integrated into a message)
		 *
		 * @param string   $request
		 * @param IElement $response
		 *
		 * @return IElement
		 */
		public function response(string $request, IElement $response): IElement;

		/**
		 * @return IElement[]
		 */
		public function getResponses(): array;
	}
