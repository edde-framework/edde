<?php
	declare(strict_types=1);
	namespace Edde\Config;

	use Edde\Obj3ct;
	use Edde\Service\Config\ConfigLoader;
	use stdClass;

	class ConfigService extends Obj3ct implements IConfigService {
		use ConfigLoader;
		/** ISection[] */
		protected $sections = [];

		/** @inheritdoc */
		public function require(string $name): ISection {
			return $this->section($name, true);
		}

		/** @inheritdoc */
		public function optional(string $name): ISection {
			return $this->section($name, false);
		}

		/**
		 * @param string $name
		 * @param bool   $required
		 *
		 * @return ISection
		 *
		 * @throws ConfigException
		 */
		protected function section(string $name, bool $required): ISection {
			if (isset($this->sections[$name])) {
				return $this->sections[$name];
			}
			$source = $this->configLoader->compile();
			if ($required && isset($source->$name) === false) {
				throw new ConfigException(sprintf('Requested section [%s] is not available!', $name));
			}
			return $this->sections[$name] = new Section($name, $source->$name ?? new stdClass());
		}
	}
