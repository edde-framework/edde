<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;

	class Packet extends Element {
		public function __construct(string $origin) {
			parent::__construct('packet');
			$this->setAttribute('version', '1.1');
			$this->setAttribute('origin', $origin);
		}

		public function element(IElement $element): Packet {
			$this->addElement('elements', $element);
			return $this;
		}

		public function elements(array $elementList): Packet {
			$this->setElementList('elements', $elementList);
			return $this;
		}

		public function reference(IElement $element): Packet {
			$this->addElement('references', $element);
			return $this;
		}

		public function references(array $elementList): Packet {
			$this->setElementList('references', $elementList);
			return $this;
		}
	}
