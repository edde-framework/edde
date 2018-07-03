<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Translator\TranslatorException;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\File\File;
	use Edde\Common\Translator\Dictionary\CsvDictionary;
	use Edde\Common\Translator\Dictionary\CsvDictionaryConverter;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\DummyDictionary;
	use Foo\Bar\EmptyDictionary;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	/**
	 * Testsuite for translator.
	 */
	class TranslatorTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITranslator
		 */
		protected $translator;

		public function testUseException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			$this->translator->use();
		}

		public function testWithoutDictionaryException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			$this->translator->onDeffered(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			$this->translator->use();
		}

		public function testEmptyDictionaryException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Cannot translate [foo]; the given id is not available in no dictionary.');
			$this->translator->registerDictionary(new EmptyDictionary());
			$this->translator->onDeffered(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			$this->translator->translate('foo');
		}

		public function testDummyDictionary() {
			$this->translator->registerDictionary(new EmptyDictionary());
			$this->translator->registerDictionary(new DummyDictionary());
			$this->translator->onDeffered(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			self::assertEquals('foo.en', $this->translator->translate('foo'));
		}

		public function testCsvDictionary() {
			$this->translator->registerDictionary($csvDictionary = $this->container->create(CsvDictionary::class));
			$csvDictionary->addFile(__DIR__ . '/assets/en.csv');
			$csvDictionary->addFile(__DIR__ . '/assets/cs.csv');
			$this->translator->registerDictionary($csvDictionary = $this->container->create(CsvDictionary::class), 'scope1');
			$csvDictionary->addFile(__DIR__ . '/assets/en-scope01.csv');
			$this->translator->registerDictionary($csvDictionary = $this->container->create(CsvDictionary::class), 'scope2');
			$csvDictionary->addFile(__DIR__ . '/assets/en-scope02.csv');
			$this->translator->setLanguage('en');
			self::assertEquals('english foo', $this->translator->translate('foo'));
			self::assertEquals('czech foo', $this->translator->translate('foo', null, 'cs'));
			$this->translator->pushScope('scope1');
			self::assertEquals('english foo1', $this->translator->translate('foo'));
			$this->translator->pushScope('scope2');
			self::assertEquals('english foo2', $this->translator->translate('foo'));
			$this->translator->pushScope(null);
			self::assertEquals('english foo', $this->translator->translate('foo'));
			self::assertEquals('czech foo', $this->translator->translate('foo', null, 'cs'));
			$this->translator->popScope();
			$this->translator->popScope();
			$this->translator->popScope();
			self::assertEquals('english foo', $this->translator->translate('foo'));
			self::assertEquals('czech foo', $this->translator->translate('foo', null, 'cs'));
		}

		public function testConverter() {
			$this->translator->registerSource(new File(__DIR__ . '/assets/dic.csv'));
			self::assertEquals('english foo', $this->translator->translate('foo', null, 'en'));
			self::assertEquals('czech foo', $this->translator->translate('foo', null, 'cs'));
		}

		protected function setUp() {
			$this->container = $container = ContainerFactory::create([
				ITranslator::class => Translator::class,
				IConverterManager::class => ConverterManager::class,
			]);
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->create(CsvDictionaryConverter::class));
			$this->translator = $container->create(ITranslator::class);
		}
	}
