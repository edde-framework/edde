<?php
	declare(strict_types=1);
	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Edde;
	use Edde\Schema\UuidSchema;
	use Edde\Service\Container\Container;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\Entity;
	use Edde\Storage\UnknownTableException;
	use Edde\Upgrade\AbstractUpgrade;
	use Edde\Upgrade\AbstractVersionService;
	use Edde\Upgrade\IUpgradeManager;
	use Edde\Upgrade\IVersionService;
	use Edde\Upgrade\UpgradeException;

	class VersionTestService extends AbstractVersionService {
		use Storage;

		/** @inheritdoc */
		public function getVersion(): ?string {
			try {
				try {
					foreach ($this->storage->value('SELECT version FROM u:schema ORDER BY stamp DESC', ['$query' => ['u' => TestUpgradeSchema::class]]) as $version) {
						return $version;
					}
					return null;
				} catch (UnknownTableException $exception) {
					$this->storage->create(TestUpgradeSchema::class);
					return null;
				}
			} catch (Throwable $exception) {
				throw new UpgradeException(sprintf('Cannot retrieve current version: %s', $exception->getMessage()), 0, $exception);
			}
		}

		/** @inheritdoc */
		public function update(string $version): IVersionService {
			$this->storage->insert(new Entity(TestUpgradeSchema::class, [
				'version' => $version,
			]));
			return $this;
		}

		/** @inheritdoc */
		public function getCollection(): Generator {
			return $this->storage->schema(TestUpgradeSchema::class, 'SELECT * FROM u:schema ORDER BY stamp DESC', ['$query' => ['u' => TestUpgradeSchema::class]]);
		}
	}

	class UpgradeConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IUpgradeManager $instance
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerUpgrades([
				$this->container->inject(new SomeUpgrade()),
				$this->container->inject(new AnotherSomeUpgrade()),
				$this->container->inject(new GenerationJumpUpgrade()),
			]);
		}
	}

	interface TestUpgradeSchema extends UuidSchema {
		public function version($unique);

		public function stamp($generator = 'stamp'): DateTime;
	}

	class SomeUpgrade extends AbstractUpgrade {
		public function getVersion(): string {
			return '1.0';
		}

		public function upgrade(): void {
		}
	}

	class AnotherSomeUpgrade extends AbstractUpgrade {
		public function getVersion(): string {
			return '1.5';
		}

		public function upgrade(): void {
		}
	}

	class GenerationJumpUpgrade extends AbstractUpgrade {
		public function getVersion(): string {
			return '2.0';
		}

		public function upgrade(): void {
		}
	}

	class ShittyInjectClass extends Edde {
		public function injectSomething(UserSchema $userSchema) {
		}
	}

	class ShittyInjectVisibilityClass extends Edde {
		protected function injectSomething(UserSchema $userSchema) {
		}
	}

	class ShittyInjectTypehintClass extends Edde {
		protected $userSchema;

		public function injectSomething($userSchema) {
		}
	}

	class ConstructorClass extends Edde {
		public $param;

		/**
		 * @param $param
		 */
		public function __construct($param) {
			$this->param = $param;
		}
	}

	interface UserSchema extends UuidSchema {
		public function login($unique = true): string;

		public function password(): string;
	}

	interface LabelSchema extends UuidSchema {
		const alias = true;

		public function name($unique): string;

		public function system($default = false): bool;
	}

	interface ProjectSchema extends UuidSchema {
		const alias = true;
		const meta = ['some-meta' => true];
		const STATUS_CREATED = 0;
		const STATUS_STARTED = 1;
		const STATUS_ENDED = 2;
		const STATUS_ARCHIVED = 3;

		public function name(): string;

		public function status($default = self::STATUS_CREATED): int;

		public function created($generator = 'stamp'): DateTime;

		public function start(): ?DateTime;

		public function end(): ?DateTime;
	}

	interface IssueSchema extends UuidSchema {
		const alias = true;

		public function name(): string;

		public function due(): ?DateTime;

		public function weight($default = 1.0): float;
	}

	interface ProjectMemberSchema extends UuidSchema {
		const relation = ['project' => 'user'];
		const alias = true;

		public function project(): ProjectSchema;

		public function user(): UserSchema;

		public function owner($default = false): bool;
	}

	interface ShittyTypeSchema extends UuidSchema {
		public function item($type = 'this-type-does-not-exists');
	}

	interface VoidSchema extends UuidSchema {
	}

	interface InvalidMetaSchema extends UuidSchema {
		const meta = false;
	}

	interface NoPrimaryKeySchema {
	}

	interface InvalidGeneratorSchema extends UuidSchema {
		public function mrdka($generator = 1);
	}

	interface InvalidFilterSchema extends UuidSchema {
		public function mrdka($filter = 1);
	}

	interface InvalidValidatorSchema extends UuidSchema {
		public function mrdka($validator = 1);
	}

	interface InvalidTypeSchema extends UuidSchema {
		public function mrdka($type = 1);
	}

	interface InvalidRelationSchema extends UuidSchema {
		const relation = ['foo' => 'bar'];

		public function mrdka($required = true, $filter = 'foo', $validator = 'bar');
	}

	interface InvalidPrimarySchema {
		const primary = 'blabla';
	}

	interface DefaultFloatValueSchema extends UuidSchema {
		public function value($default = 0): ?float;
	}

	interface JsonSchema extends UuidSchema {
		public function someJson($type = 'json');
	}

	interface Base64Schema extends UuidSchema {
		public function someBinary($type = 'binary');
	}
