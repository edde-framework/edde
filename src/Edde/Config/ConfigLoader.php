<?php
	declare(strict_types=1);
	namespace Edde\Config;

	use Edde\Object;
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
		public function require(string $config): IConfigLoader {
			return $this->config($config, true);
		}

		/** @inheritdoc */
		public function optional(string $config): IConfigLoader {
			return $this->config($config, false);
		}

		/** @inheritdoc */
		public function clear(): IConfigLoader {
			$this->configs = [];
			$this->config = null;
			return $this;
		}

		/** @inheritdoc */
		public function compile(): stdClass {
			if ($this->config) {
				return $this->config;
			}
			$this->config = new stdClass();
			foreach ($this->configs as [$file, $required]) {
				if (is_readable($file) === false) {
					if ($required) {
						throw new ConfigException(sprintf('Required config file [%s] is not available!', $file));
					}
					continue;
				} else if (($source = @parse_ini_file($file, true, INI_SCANNER_TYPED)) === false) {
					if ($required) {
						throw new ConfigException(sprintf('Required config file [%s] cannot be parsed.', $file));
					}
					continue;
				}
				foreach ($source as $k => $v) {
					$this->config->$k = (object)$v;
				}
			}
			return $this->config;
		}

		protected function config(string $config, bool $required): IConfigLoader {
			$this->config = null;
			$this->configs[$config] = [$config, $required];
			return $this;
		}
	}
