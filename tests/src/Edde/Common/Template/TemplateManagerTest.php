<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Html\IHtmlGenerator;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Html\Html5Generator;
	use Edde\Common\Resource\UnknownResourceException;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\ConverterManagerConfigurator;
	use Edde\Ext\Template\CompilerConfigurator;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class TemplateManagerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;

		public function testException() {
			$this->expectException(UnknownResourceException::class);
			$this->expectExceptionMessage('Requested unknown resource [moo].');
			$template = $this->templateManager->template();
			$template->template('moo');
			$template->execute();
		}

		protected function setUp() {
			$this->container = ContainerFactory::container([
				IRootDirectory::class     => ContainerFactory::instance(RootDirectory::class, [__DIR__]),
				ITemplateDirectory::class => ContainerFactory::proxy(IRootDirectory::class, 'directory', ['temp']),
				ITemplateManager::class   => TemplateManager::class,
				ICompiler::class          => Compiler::class,
				ICacheStorage::class      => InMemoryCacheStorage::class,
				IHtmlGenerator::class     => Html5Generator::class,
				new ClassFactory(),
			], [
				IConverterManager::class => ConverterManagerConfigurator::class,
				ICompiler::class         => CompilerConfigurator::class,
			]);
			$tempDirectory = $this->container->create(ITemplateDirectory::class);
			$tempDirectory->purge();
			$this->rootDirectory = $this->container->create(IRootDirectory::class);
			$this->rootDirectory->normalize();
			$this->templateManager = $this->container->create(ITemplateManager::class);
		}
	}
