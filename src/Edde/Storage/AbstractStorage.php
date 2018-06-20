<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\ISection;
	use Edde\Hydrator\IHydrator;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Hydrator\HydratorManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Transaction\AbstractTransaction;
	use Generator;
	use Throwable;
	use function preg_match_all;
	use function str_replace;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
		use HydratorManager;
		use SchemaManager;
		/** @var string */
		protected $config;
		/** @var ISection */
		protected $section;

		/**
		 * @param string $config
		 */
		public function __construct(string $config) {
			$this->config = $config;
		}

		/** @inheritdoc */
		public function hydrate(string $query, IHydrator $hydrator, array $params = []): Generator {
			foreach ($this->fetch($query, $params) as $item) {
				yield $hydrator->hydrate($item);
			}
		}

		/** @inheritdoc */
		public function value(string $query, array $params = []): Generator {
			return $this->hydrate($query, $this->hydratorManager->single(), $params);
		}

		/** @inheritdoc */
		public function schema(string $name, string $query, array $params = []): Generator {
			return $this->hydrate($query, $this->hydratorManager->schema($name), $params);
		}

		/** @inheritdoc */
		public function query(string $query, array $schemas): string {
			$matches = null;
			preg_match_all('~([a-zA-Z0-9]+):schema~', $query, $matches);
			foreach ($matches[1] as $index => $alias) {
				if (isset($schemas[$alias]) === false) {
					throw new StorageException(sprintf('Cannot translate unknown alias [%s] to schema name.', $alias));
				}
				$query = str_replace($matches[0][$index], $this->delimit($this->schemaManager->getSchema($schemas[$alias])->getRealName()), $query);
			}
			preg_match_all('~([a-zA-Z0-9]+):delimit~', $query, $matches);
			foreach ($matches[1] as $index => $alias) {
				$query = str_replace($matches[0][$index], $this->delimit($matches[1][$index]), $query);
			}
			return $query;
		}

		/**
		 * @param Throwable $throwable
		 *
		 * @return Throwable
		 */
		public function exception(Throwable $throwable): Throwable {
			return $throwable;
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
		}
	}
