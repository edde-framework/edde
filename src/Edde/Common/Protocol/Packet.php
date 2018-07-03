<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;

	class Packet extends Element implements IPacket {
		public function __construct(string $origin) {
			parent::__construct('packet');
			$this->setAttribute('version', '1.1');
			$this->setAttribute('origin', $origin);
		}

		/**
		 * @inheritdoc
		 */
		public function element(IElement $element): IPacket {
			$this->addElement('elements', $element);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function elements(array $elementList): IPacket {
			$this->setElementList('elements', $elementList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function reference(IElement $element): IPacket {
			$this->addElement('references', $element);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function references(array $elementList): IPacket {
			$this->setElementList('references', $elementList);
			return $this;
		}
	}
