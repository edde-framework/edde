<?php
	declare(strict_types=1);
	namespace Edde\Service\Config;

	use Edde\Api\Config\IConfigLoader;
	use Edde\Common\Object\Object;
	use Edde\Exception\Config\RequiredConfigException;
	use stdClass;
	use function is_readable;
	use function parse_ini_file;
	use const INI_SCANNER_TYPED;

	class ConfigLoader extends Object implements IConfigLoader {
		/** @var array */
		protected $configs = [];
		/** @var stdClass */
		protected $config;

		/** @inheritdoc */
		public function require(string $config, bool $required = true): IConfigLoader {
			$this->configs[$config] = [$config, $required];
			return $this;
		}

		/** @inheritdoc */
		public function optional(string $config): IConfigLoader {
			return $this;
		}

		/** @inheritdoc */
		public function compile(): stdClass {
			if ($this->config) {
				return $this->config;
			}
			$this->config = new stdClass();
			foreach ($this->configs as [$file, $required]) {
				if ($required && is_readable($file) === false) {
					throw new RequiredConfigException(sprintf('Required config file [%s] is not available!', $file));
				} else if (($source = parse_ini_file($file, true, INI_SCANNER_TYPED)) === false && $required) {
					throw new RequiredConfigException(sprintf('Required config file [%s] cannot be parsed.', $file));
				}
				foreach ($source as $k => $v) {
					$this->config->$k = (object)$v;
				}
			}
			return $this->config;
		}
	}
