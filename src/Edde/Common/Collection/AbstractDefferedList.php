<?php
	declare(strict_types = 1);

	namespace Edde\Common\Collection;

	use Edde\Api\Collection\IList;
	use Edde\Common\Deffered\DefferedTrait;

	class AbstractDefferedList extends AbstractList {
		use DefferedTrait;

		public function isEmpty(): bool {
			$this->use();
			return parent::isEmpty();
		}

		public function get(string $name, $default = null) {
			$this->use();
			return parent::get($name, $default);
		}

		public function has(string $name): bool {
			$this->use();
			return parent::has($name);
		}

		public function array(): array {
			$this->use();
			return parent::array();
		}

		public function remove(string $name): IList {
			$this->use();
			return parent::remove($name);
		}

		public function getIterator() {
			$this->use();
			return parent::getIterator();
		}
	}
