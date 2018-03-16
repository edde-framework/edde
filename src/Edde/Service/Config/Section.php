<?php
	declare(strict_types=1);
	namespace Edde\Service\Config;

	use Edde\Api\Config\ISection;
	use Edde\Config\ConfigException;
	use Edde\Object;
	use stdClass;

	class Section extends Object implements ISection {
		/** @var string */
		protected $name;
		/** @var stdClass */
		protected $section;

		/**
		 * @param string   $name
		 * @param stdClass $section
		 */
		public function __construct(string $name, stdClass $section) {
			$this->name = $name;
			$this->section = $section;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function require(string $name) {
			if (isset($this->section->$name) === false) {
				throw new ConfigException(sprintf('Required section value [%s::%s] is not available!', $this->name, $name));
			}
			return $this->section->$name;
		}

		/** @inheritdoc */
		public function optional(string $name, $default = null) {
			return $this->section->$name ?? $default;
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			return $this->section;
		}
	}
