<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Service\Config\ConfigService;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Transaction\AbstractTransaction;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
		use SchemaManager;
		/** @var string */
		protected $config;

		/**
		 * @param string $config
		 */
		public function __construct(string $config) {
			$this->config = $config;
		}
	}
