<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\ISection;
	use Edde\Hydrator\IHydrator;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Hydrator\HydratorManager;
	use Edde\Transaction\AbstractTransaction;
	use Generator;
	use Throwable;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
		use HydratorManager;
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
