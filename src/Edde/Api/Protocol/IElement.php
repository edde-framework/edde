<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Node\INode;

	interface IElement extends INode {
		/**
		 * return element type (basicaly node name)
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * @param string $type
		 *
		 * @return bool
		 */
		public function isType(string $type): bool;

		/**
		 * @param string $id
		 *
		 * @return IElement
		 */
		public function setId(string $id): IElement;

		/**
		 * return the element id; format is free to use, but it should be generally unique (for example guid)
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * @param bool $async
		 *
		 * @return IElement
		 */
		public function async(bool $async = true): IElement;

		/**
		 * @return bool
		 */
		public function isAsync(): bool;

		/**
		 * @param IElement $element
		 *
		 * @return IElement
		 */
		public function setReference(IElement $element): IElement;

		/**
		 * @return bool
		 */
		public function hasReference(): bool;

		/**
		 * @return string
		 */
		public function getReference(): string;

		/**
		 * replace current data by new one
		 *
		 * @param array $data
		 *
		 * @return IElement
		 */
		public function data(array $data): IElement;

		/**
		 * @return array
		 */
		public function getData(): array;

		/**
		 * add the given element under the given node name
		 *
		 * @param string   $name
		 * @param IElement $element
		 *
		 * @return IElement
		 */
		public function addElement(string $name, IElement $element): IElement;

		/**
		 * @param string $name
		 *
		 * @return IElement|null
		 */
		public function getElementNode(string $name);

		/**
		 * @param string     $name
		 * @param IElement[] $elementList
		 *
		 * @return IElement
		 */
		public function setElementList(string $name, array $elementList): IElement;

		/**
		 * @param string $name
		 *
		 * @return IElement[]
		 */
		public function getElementList(string $name): array;

		/**
		 * get the element by the given ID
		 *
		 * @param string $id
		 *
		 * @return IElement
		 */
		public function getReferenceBy(string $id): IElement;

		/**
		 * @param string $id
		 *
		 * @return array
		 */
		public function getReferenceList(string $id): array;
	}
