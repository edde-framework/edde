<?php
	declare(strict_types=1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Api\Schema\SchemaException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Factory for... creating crates.
	 */
	class CrateFactory extends Object implements ICrateFactory {
		use LazySchemaManagerTrait;
		use LazyContainerTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 * @throws SchemaException
		 * @throws CrateException
		 */
		public function crate(string $schema, array $load = null, string $crate = null): ICrate {
			/** @var $crate ICrate */
			$crate = $this->container->create(ICrate::class, [], __METHOD__);
			$this->schemaManager->setup();
			$crate->setSchema($schema = $this->schemaManager->getSchema($schema));
			foreach ($schema->getPropertyList() as $property) {
				$crate->addProperty(new Property($property));
			}
			$load ? $this->load($crate, $load) : null;
			return $crate;
		}

		/**
		 * @inheritdoc
		 */
		public function collection(string $schema, string $crate = null): ICollection {
			return $this->container->create(Collection::class, [
				$schema,
				$crate,
			], __METHOD__);
		}

		/**
		 * @inheritdoc
		 * @throws SchemaException
		 * @throws CrateException
		 */
		public function build(array $crateList): array {
			$crates = [];
			foreach ($crateList as $schema => $source) {
				$this->load($crates[] = $this->crate($schema), $source);
			}
			return $crates;
		}

		/**
		 * @param ICrate $crate
		 * @param array  $source
		 *
		 * @return ICrate
		 * @throws SchemaException
		 * @throws CrateException
		 */
		protected function load(ICrate $crate, array $source) {
			$schema = $crate->getSchema();
			foreach ($source as $property => $value) {
				if ($schema->hasCollection($property = (string)$property)) {
					$schemaCollection = $schema->getCollection($property);
					$targetSchema = $schemaCollection->getTarget()->getSchema()->getSchemaName();
					$crate->collection($property, $collection = $this->collection($targetSchema));
					/** @var $value array */
					foreach ($value as $collectionValue) {
						if (is_array($collectionValue) === false) {
							throw new CrateException(sprintf('Cannot push source value into the crate [%s]; value [%s] is not an array (collection).', $schema->getSchemaName(), $property));
						}
						$collection->addCrate($this->crate($targetSchema, $collectionValue));
					}
					unset($source[$property]);
				} else if (is_array($value) && $schema->hasLink($property)) {
					$targetSchema = $schema->getLink($property)->getTarget()->getSchema()->getSchemaName();
					$crate->link($property, $this->crate($targetSchema, $value));
					unset($source[$property]);
				}
			}
			$crate->push($source);
			return $crate;
		}
	}
