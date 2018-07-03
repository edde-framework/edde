<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Crate\LazyCrateDirectoryTrait;
	use Edde\Api\File\FileException;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaCollection;
	use Edde\Api\Schema\ISchemaLink;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Simple crate php class generator.
	 */
	class CrateGenerator extends AbstractDeffered implements ICrateGenerator {
		use LazySchemaManagerTrait;
		use LazyCrateDirectoryTrait;
		use LazyTempDirectoryTrait;
		use CacheTrait;
		/**
		 * @var string
		 */
		protected $parent;

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function generate(bool $force = false): ICrateGenerator {
			if ($this->isUsed()) {
				return $this;
			}
			$this->use();
			$lock = $this->tempDirectory->file('.crate-generator');
			$lock->lock();
			/** @noinspection NotOptimalIfConditionsInspection */
			if (($crateList = $this->cache->load('crate-list', [])) === [] || $force === true) {
				$this->crateDirectory->purge();
				foreach ($this->schemaManager->getSchemaList() as $schema) {
					$crateList[] = $schemaName = $schema->getSchemaName();
					FileUtils::createDir($path = FileUtils::normalize($this->crateDirectory->getDirectory() . '/' . $schema->getNamespace()));
					foreach ($this->compile($schema) as $name => $source) {
						file_put_contents($path . '/' . $schema->getName() . '.php', $source);
					}
				}
				$this->cache->save('crate-list', $crateList);
			}
			$this->crateDirectory->save('loader.php', "<?php
	Edde\\Common\\Autoloader::register(null, __DIR__, false);	
");
			$this->include();
			$lock->unlock();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function compile(ISchema $schema): array {
			$this->use();
			$sourceList = [];
			$source[] = "<?php\n";
			$source[] = "\tdeclare(strict_types = 1);\n\n";
			if (($namespace = $schema->getNamespace()) !== '') {
				$source[] = "\tnamespace $namespace;\n\n";
			}
			$source[] = sprintf("\tuse %s;\n", ICollection::class);
			$source[] = sprintf("\tuse %s;\n", $this->parent);
			$source[] = "\n";
			$parent = explode('\\', $this->parent);
			if (($implements = $schema->getMeta('implements', '')) !== '') {
				$implements = ' implements \\' . implode(', \\', (array)$implements);
			}
			$source[] = sprintf("\tclass %s extends %s%s {\n", $schema->getName(), end($parent), $implements);
			foreach ($schema->getPropertyList() as $schemaProperty) {
				$source[] = $this->generateSchemaProperty($schemaProperty);
			}
			foreach ($schema->getCollectionList() as $schemaCollection) {
				$source[] = $this->generateCollection($schemaCollection);
			}
			foreach ($schema->getLinkList() as $schemaLink) {
				$source[] = $this->generateLink($schemaLink);
			}
			$source[] = "\t}\n";
			$sourceList[$schema->getSchemaName()] = implode('', $source);
			return $sourceList;
		}

		/**
		 * @param ISchemaProperty $schemaProperty
		 *
		 * @return string
		 */
		protected function generateSchemaProperty(ISchemaProperty $schemaProperty) {
			$source[] = $this->generateGetter($schemaProperty);
			$source[] = $this->generateSetter($schemaProperty);
			if ($schemaProperty->isArray()) {
				$source[] = $this->generateArray($schemaProperty);
			}
			$source[] = '';
			return implode("\n", $source);
		}

		/**
		 * @param ISchemaProperty $schemaProperty
		 *
		 * @return string
		 */
		protected function generateGetter(ISchemaProperty $schemaProperty) {
			$source[] = "\t\t/**\n";
			$type = $schemaProperty->isArray() ? 'array
				' : $schemaProperty->getType();
			$source[] = sprintf("\t\t * @return %s\n", $type);
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function get%s()%s {\n", StringUtils::toCamelCase($schemaProperty->getName()), $schemaProperty->isRequired() ? (': ' . $type) : '');
			$source[] = sprintf("\t\t\treturn \$this->get('%s');\n", $schemaProperty->getName());
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		/**
		 * @param ISchemaProperty $schemaProperty
		 *
		 * @return string
		 */
		protected function generateSetter(ISchemaProperty $schemaProperty) {
			$parameter = '_' . StringUtils::firstLower($camelCase = StringUtils::toCamelCase($propertyName = $schemaProperty->getName()));
			$source[] = "\t\t/**\n";
			$type = $schemaProperty->isArray() ? 'array
				' : $schemaProperty->getType();
			$source[] = sprintf("\t\t * @param %s $%s\n", $type, $parameter);
			$source[] = "\t\t * \n";
			$source[] = "\t\t * @return \$this\n";
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function set%s(%s \$%s%s) {\n", $camelCase, $type, $parameter, $schemaProperty->isRequired() ? '' : ($schemaProperty->isArray() ? ' = []' : ' = null'));
			$source[] = sprintf("\t\t\t\$this->set('%s', \$%s);\n", $propertyName, $parameter);
			$source[] = "\t\t\treturn \$this;\n";
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		/**
		 * @param ISchemaProperty $schemaProperty
		 *
		 * @return string
		 */
		protected function generateArray(ISchemaProperty $schemaProperty) {
			$parameter = StringUtils::firstLower($camelCase = StringUtils::toCamelCase($propertyName = $schemaProperty->getName()));
			$source[] = "\t\t/**\n";
			$source[] = sprintf("\t\t * @param %s $%s\n", $schemaProperty->getType(), $parameter);
			$source[] = "\t\t * \n";
			$source[] = "\t\t * @return \$this\n";
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function add%s(%s \$%s, \$key = null) {\n", $camelCase, $schemaProperty->getType(), $parameter);
			$source[] = sprintf("\t\t\t\$this->add('%s', \$%s, \$key);\n", $propertyName, $parameter);
			$source[] = "\t\t\treturn \$this;\n";
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		/**
		 * @param ISchemaCollection $schemaCollection
		 *
		 * @return string
		 */
		protected function generateCollection(ISchemaCollection $schemaCollection) {
			$source[] = '';
			$source[] = "\t\t/**\n";
			$source[] = "\t\t * \n";
			$source[] = sprintf("\t\t * @return %s\n", StringUtils::extract(ICollection::class, '\\', -1));
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function collection%s() {\n", StringUtils::toCamelCase($collectionName = $schemaCollection->getName()));
			$source[] = sprintf("\t\t\treturn \$this->collection('%s');\n", $collectionName);
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		/**
		 * @param ISchemaLink $schemaLink
		 *
		 * @return string
		 */
		protected function generateLink(ISchemaLink $schemaLink) {
			$targetSchemaName = $schemaLink->getTarget()
				->getSchema()
				->getSchemaName();
			$source[] = '';
			$source[] = "\t\t/**\n";
			$source[] = sprintf("\t\t * @return \\%s\n", $targetSchemaName);
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function link%s() {\n", StringUtils::toCamelCase($linkName = $schemaLink->getName()));
			$source[] = sprintf("\t\t\treturn \$this->getLink('%s');\n", $linkName);
			$source[] = "\t\t}\n";
			$source[] = "\n";
			$source[] = sprintf("\t\tpublic function set%sLink(\\%s \$%s) {\n", StringUtils::toCamelCase($linkName = $schemaLink->getName()), $targetSchemaName, $linkName);
			$source[] = sprintf("\t\t\t\$this->link('%s', \$%s);\n", $linkName, $linkName);
			$source[] = sprintf("\t\t\treturn \$this;\n");
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		/**
		 * @inheritdoc
		 */
		public function include (): ICrateGenerator {
			/** @noinspection UnnecessaryParenthesesInspection */
			(function (IResource $resource) {
				require_once $resource->getUrl();
			})($this->crateDirectory->file('loader.php'));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			parent::prepare();
			$this->cache();
			$this->crateDirectory->create();
			$this->parent = Crate::class;
		}
	}
