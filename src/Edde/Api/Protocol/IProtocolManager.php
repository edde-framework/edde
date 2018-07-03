<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	interface IProtocolManager extends IConfigurable {
		/**
		 * queue element to be included in packet created by self::createPacket();
		 * this method is useful to collect elements around the world and then send
		 * them to somewhere (e.g. client)
		 *
		 * @param IElement    $element
		 * @param string|null $store
		 *
		 * @return IProtocolManager
		 */
		public function queue(IElement $element, string $store = null): IProtocolManager;

		/**
		 * @param IElement[]  $elementList
		 * @param string|null $store
		 *
		 * @return IProtocolManager
		 */
		public function queueList(array $elementList, string $store = null): IProtocolManager;

		/**
		 * create packet with payload of all available elements and references
		 *
		 * @param string|null   $store
		 * @param IElement|null $reference
		 *
		 * @return IElement
		 */
		public function createPacket(string $store = null, IElement $reference = null): IElement;

		/**
		 * execute the given element; if the element is async, it's moved to a job queue
		 *
		 * @param IElement $element
		 *
		 * @return mixed
		 */
		public function execute(IElement $element): IElement;
	}
