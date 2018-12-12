<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use DateTime;
	use Edde\Schema\SchemaException;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\TestCase;
	use SomeSchema;

	class FilterManagerTest extends TestCase {
		use FilterManager;
		use SchemaManager;

		/**
		 * @throws FilterException
		 */
		public function testGetFilterException() {
			$this->expectException(FilterException::class);
			$this->expectExceptionMessage('Requested unknown filter [nope].');
			$this->filterManager->getFilter('nope');
		}

		/**
		 * @throws FilterException
		 * @throws SchemaException
		 */
		public function testInputFilter() {
			$this->schemaManager->load(SomeSchema::class);
			$filter = $this->filterManager->input($input = [
				'date' => '2015-08-03T06:34:11.000000',
				'bint' => 1,
				'uuid' => '123',
			], SomeSchema::class, 'type');
			self::assertEquals($output = [
				'date' => new DateTime('2015-08-03 06:34:11.000000'),
				'bint' => true,
				'uuid' => '123',
			], $filter);
			self::assertEquals($this->filterManager->output($output, SomeSchema::class, 'type'), $input);
		}
	}
