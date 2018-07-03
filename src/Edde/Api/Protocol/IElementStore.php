<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	/**
	 * This implementation could track all incoming and processed Elements and to be a general
	 * store for them. That means this should be kind of persistant storage, but it should NOT
	 * directly use store manager select method.
	 */
	interface IElementStore extends IConfigurable {
		/**
		 * save the given element to the store
		 *
		 * @param IElement $element
		 *
		 * @return IElementStore
		 */
		public function save(IElement $element): IElementStore;

		/**
		 * is the given guid present?
		 *
		 * @param string $guid
		 *
		 * @return bool
		 */
		public function has(string $guid): bool;

		/**
		 * load the given element from the store; exception should be thrown if the element does not exists
		 *
		 * @note element COULD not exist on top level, but could be hidden in some packet
		 *
		 * @param string $guid
		 *
		 * @return IElement|IElementStore
		 */
		public function load(string $guid): IElement;

		/**
		 * @param string $guid
		 *
		 * @return IElementStore
		 */
		public function remove(string $guid): IElementStore;

		/**
		 * get list of elements referencing the given guid
		 *
		 * @return \Traversable|IElement[]
		 */
		public function getReferenceListBy(string $referenceId);
	}
