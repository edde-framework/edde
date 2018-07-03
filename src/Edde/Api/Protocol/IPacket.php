<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IPacket extends IElement {
		/**
		 * shortcut for add a new element to elements
		 *
		 * @param IElement $element
		 *
		 * @return IPacket
		 */
		public function element(IElement $element): IPacket;

		/**
		 * @param array $elementList
		 *
		 * @return IPacket
		 */
		public function elements(array $elementList): IPacket;

		/**
		 * shortuct to add a new element to references
		 *
		 * @param IElement $element
		 *
		 * @return IPacket
		 */
		public function reference(IElement $element): IPacket;

		/**
		 * @param array $elementList
		 *
		 * @return IPacket
		 */
		public function references(array $elementList): IPacket;
	}
