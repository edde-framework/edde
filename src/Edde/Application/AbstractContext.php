<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Object;
	use Edde\Service\Crypt\RandomService;

	abstract class AbstractContext extends Object implements IContext {
		use RandomService;
		protected $uuid;

		/** @inheritdoc */
		public function getId(): string {
			return static::class;
		}

		/** @inheritdoc */
		public function getUuid(): string {
			return $this->uuid ?: $this->uuid = $this->randomService->uuid($this->getId());
		}

		/** @inheritdoc */
		public function cascade(string $delimiter, string $name = null): array {
			return [];
		}
	}
