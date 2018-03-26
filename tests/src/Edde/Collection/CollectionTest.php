<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use BarSchema;
	use Edde\Connection\IConnection;
	use Edde\Connection\MysqlConnection;
	use Edde\Container\ContainerException;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\Service\Connection\Connection;
	use Edde\Service\Transaction\Transaction;
	use Edde\TestCase;
	use FooSchema;

	class CollectionTest extends TestCase {
		use Transaction;
		use Connection;

		/**
		 * @throws ContainerException
		 */
		public function testCreateSchema() {
			/** @var $collection ICollection */
			$collection = $this->container->inject(new Collection());
			$collection->use(FooSchema::class, 'foo');
			$collection->use(BarSchema::class, 'bar');
			$collection->create();
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IConnection::class, MysqlConnection::class), IConnection::class);
		}
	}
