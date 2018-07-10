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
	use function array_keys;
	use function count;
	use function implode;
	use function preg_match_all;
	use function sha1;
	use function str_replace;
	use function uasort;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
		use HydratorManager;
		use SchemaManager;
		/** @var string */
		protected $config;
		/** @var ISection */
		protected $section;
		/** @var string[] */
		protected $queries = [];

		/**
		 * @param string $config
		 */
		public function __construct(string $config) {
			$this->config = $config;
		}

		/** @inheritdoc */
		public function hydrate(string $query, IHydrator $hydrator, array $params = []): Generator {
			if (isset($params['$query'])) {
				$query = $this->query($query, $params['$query']);
				unset($params['$query']);
			}
			foreach ($this->fetch($query, $params) as $item) {
				yield $hydrator->hydrate($item);
			}
		}

		/** @inheritdoc */
		public function single(string $name, string $query, array $params = []): IEntity {
			foreach ($this->schema($name, $query, $params) as $entity) {
				return $entity;
			}
			throw new EmptyEntityException(sprintf('Could not fetch single entity [%s].', $name));
		}

		/** @inheritdoc */
		public function value(string $query, array $params = []): Generator {
			return $this->hydrate($query, $this->hydratorManager->single(), $params);
		}

		/** @inheritdoc */
		public function schema(string $name, string $query, array $params = []): Generator {
			foreach ($this->hydrate($query, $this->hydratorManager->schema($name), $params) as $item) {
				yield new Entity($name, $item);
			}
		}

		/** @inheritdoc */
		public function query(string $query, array $schemas): string {
			if (isset($this->queries[$cacheId = sha1($query . implode(':', $schemas) . implode(':', array_keys($schemas)))])) {
				return $this->queries[$cacheId];
			}
			if (count($this->queries) > 128) {
				array_shift($this->queries);
			}
			$matches = null;
			preg_match_all('~([a-zA-Z0-9]+):schema~', $query, $matches);
			uasort($matches[0], function ($a, $b) {
				return strlen($b) <=> strlen($a);
			});
			foreach ($matches[0] as $index => $replace) {
				if (isset($schemas[$alias = $matches[1][$index]]) === false) {
					throw new StorageException(sprintf('Cannot translate unknown alias [%s] to schema name.', $alias));
				}
				$query = str_replace($replace, $this->delimit($this->schemaManager->getSchema($schemas[$alias])->getRealName()), $query);
			}
			preg_match_all('~([a-zA-Z0-9]+):delimit~', $query, $matches);
			uasort($matches[0], function ($a, $b) {
				return strlen($b) <=> strlen($a);
			});
			foreach ($matches[0] as $index => $replace) {
				$query = str_replace($replace, $this->delimit($matches[1][$index]), $query);
			}
			return $this->queries[$cacheId] = $query;
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
		}
	}
