<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\ISection;
	use Edde\Service\Config\ConfigService;
	use Edde\Transaction\AbstractTransaction;
	use Throwable;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
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

		/**
		 * @param Throwable $throwable
		 *
		 * @return Throwable
		 */
		public function resolveException(Throwable $throwable): Throwable {
			return $throwable;
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
		}
	}
