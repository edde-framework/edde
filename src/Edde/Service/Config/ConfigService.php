<?php
	declare(strict_types=1);
	namespace Edde\Service\Config;

	use Edde\Api\Config\IConfigService;
	use Edde\Api\Config\ISection;
	use Edde\Exception\Config\RequiredConfigException;
	use Edde\Exception\Config\RequiredSectionException;
	use Edde\Inject\Config\ConfigLoader;
	use Edde\Object;
	use stdClass;

	class ConfigService extends Object implements IConfigService {
		use ConfigLoader;
		/** ISection[] */
		protected $sections = [];

		/** @inheritdoc */
		public function require(string $name): ISection {
			return $this->section($name, true);
		}

		/**
		 * @inheritdoc
		 *
		 * @throws RequiredConfigException
		 * @throws RequiredSectionException
		 */
		public function optional(string $name): ISection {
			return $this->section($name, false);
		}

		/**
		 * @param string $name
		 * @param bool   $required
		 *
		 * @return ISection
		 *
		 * @throws RequiredSectionException
		 * @throws RequiredConfigException
		 */
		protected function section(string $name, bool $required): ISection {
			if (isset($this->sections[$name])) {
				return $this->sections[$name];
			}
			$source = $this->configLoader->compile();
			if ($required && isset($source->$name) === false) {
				throw new RequiredSectionException(sprintf('Requested section [%s] is not available!', $name));
			}
			return $this->sections[$name] = new Section($name, $source->$name ?? new stdClass());
		}
	}
