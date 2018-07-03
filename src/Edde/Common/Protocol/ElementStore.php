<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IElementStore;
	use Edde\Api\Store\Inject\Store;
	use Edde\Common\Object\Object;
	use Edde\Common\Protocol\Exception\UnknownElementException;

	class ElementStore extends Object implements IElementStore {
		use Store;

		/**
		 * @inheritdoc
		 */
		public function save(IElement $element): IElementStore {
			$this->store->set($element->getId(), $element);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function has(string $guid): bool {
			return $this->store->has($guid);
		}

		/**
		 * @inheritdoc
		 */
		public function load(string $guid): IElement {
			if (($element = $this->store->get($guid)) === null) {
				throw new UnknownElementException(sprintf('Requested unknown Element [%s]. Have you choosen right store?', $guid));
			}
			return $element;
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $guid): IElementStore {
			$this->store->remove($guid);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceListBy(string $referenceId) {
			foreach ($this->store->iterate() as $element) {
				if ($element instanceof IElement && $element->hasReference() && $element->getReference() === $referenceId) {
					yield $element;
				}
			}
		}
	}
