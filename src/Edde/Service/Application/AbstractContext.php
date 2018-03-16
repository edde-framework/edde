<?php
	declare(strict_types=1);
	namespace Edde\Service\Application;

	use Edde\Api\Application\IContext;
	use Edde\Common\Object\Object;

	abstract class AbstractContext extends Object implements IContext {
		/**
		 * @return string
		 */
		public function getId(): string {
			return static::class;
		}

		/**
		 * @inheritdoc
		 */
		public function getUuid(): string {
			return sha1($this->getId());
		}

		/**
		 * @inheritdoc
		 */
		public function cascade(string $delimiter, string $name = null): array {
			return [];
		}
	}
