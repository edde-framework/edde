<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Html\ITemplateDirectory;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Application\Request;
	use Edde\Common\AssetsDirectory;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\File\File;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Html\AbstractHtmlTemplate;
	use Edde\Common\Html\ContainerControl;
	use Edde\Common\Html\Macro\HtmlMacro;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Html\TemplateDirectory;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Http\RequestUrl;
	use Edde\Common\Link\AbstractLinkGenerator;
	use Edde\Common\Link\ControlLinkGenerator;
	use Edde\Common\Link\LinkFactory;
	use Edde\Common\Resource\ResourceList;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Translator\Dictionary\CsvDictionaryConverter;
	use Edde\Common\Translator\Translator;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use Edde\Ext\Template\DefaultMacroSet;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets.php';

	/**
	 * Test covering all template features from "real world" usage.
	 */
	class TemplateManagerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;
		/**
		 * @var IResourceList
		 */
		protected $styleSheetList;
		/**
		 * @var IResourceList
		 */
		protected $javaScriptList;
		protected $flag = false;

		public function call() {
			$this->flag = true;
		}

		public function testComplex() {
			$file = $this->templateManager->template(__DIR__ . '/template/complex/layout.xml', [
				__DIR__ . '/template/complex/to-be-used.xml',
			]);
			$template = AbstractHtmlTemplate::template($file, $this->container);
			$template->snippet($this->container->inject($control = new \SomeCoolControl()));
			$control->addClass('root');
			$control->dirty();
			self::assertEquals(file_get_contents(__DIR__ . '/template/complex/result.xml'), $control->render());

			self::assertInstanceOf(DivControl::class, $control->someVariable);
			$control->someVariable->dirty();
			self::assertEquals('<div class="this-will-be-loaded-on-demand">
	<span class="Hey, I\'m alive!"></span>
	<div>
		<div class="hello there!"></div>
	</div>
	<div class="poo-class">poo</div>
</div>
', $control->someVariable->render(-1));

			self::assertInstanceOf(SpanControl::class, $control->spanControl);
			$control->spanControl->dirty();
			self::assertEquals('<span class="foo"></span>
', $control->spanControl->render(-1));

			self::assertInstanceOf(SpanControl::class, $control->includedVariable);
			$control->includedVariable->dirty();
			self::assertEquals('<span class="included-variable">
	<div class="foo-bar"></div>
	<div class="boo-far">
		<span class="poo">hello!</span>
	</div>
	<div class="foo-foo"></div>
</span>
', $control->includedVariable->render(-1));

			$cssList = [
				(new File(__DIR__ . '/../../../../../src/Edde/assets/css/foundation.min.css'))->getUrl()
					->getAbsoluteUrl(),
				(new File(__DIR__ . '/template/complex/foo/bar/boo.css'))->getUrl()
					->getAbsoluteUrl(),
			];
			$pathList = $this->styleSheetList->getPathList();
			sort($cssList);
			sort($pathList);
			self::assertEquals($cssList, $pathList);
			/**
			 * contorls can generate javascript with proprietary path, so it is not easy to test it
			 *
			 * at least this is most simple test for executed javascript
			 *
			 * 2 is for 1 button and 1 for explicit js macro
			 */
			self::assertCount(1, $this->javaScriptList->getPathList());

			$template->snippet($this->container->inject($control = new \SomeCoolControl()), 'deep-block');
			$control->addClass('root');
			$control->dirty();
			self::assertEquals('<div class="root">
	<div class="really-deep-div-here">
		<div class="deepness-of-a-deep" something="ou-yay!">foo</div>
	</div>
</div>
', $control->render());

			$template->snippet($this->container->inject($control = new \SomeCoolControl()), 'overkill-block');
			$control->addClass('root');
			$control->dirty();
			self::assertEquals('<div class="root">
	<div class="overkilled">
		<div class="abc">a</div>
		<div class="button" data-action="abc"></div>
	</div>
	<div class="overkilled">
		<div class="def">b</div>
		<div class="button" data-action="def"></div>
	</div>
	<div class="overkilled">
		<div class="ghi">c</div>
		<div class="button" data-action="ghi"></div>
	</div>
</div>
', $control->render());

			$template->snippet($this->container->inject($control = new \SomeCoolControl()), 'beast-on-demand2');
			$control->addClass('root');
			$control->dirty();
			self::assertEquals('<div class="root">
	<div class="this-will-be-loaded-on-demand">
		<span class="Hey, I\'m alive!"></span>
		<div>
			<div class="hello there!"></div>
		</div>
		<div class="poo-class">poo</div>
	</div>
	<div class="really-deep-div-here">
		<div class="deepness-of-a-deep" something="ou-yay!">foo</div>
	</div>
</div>
', $control->render());

			$template->snippet($this->container->inject($control = new DivControl()), 'the-name-of-this-snippet');
			$control->addClass('root');
			$control->dirty();
			self::assertEquals('<div class="root">
	<div class="thie-piece-will-not-be-visible">
		<div class="foo"></div>
	</div>
</div>
', $control->render());
			$template->snippet($this->container->inject($containerControl = new ContainerControl()), 'beast-on-demand');
			$containerControl->dirty();
			self::assertEquals('<div class="this-will-be-loaded-on-demand">
	<span class="Hey, I\'m alive!"></span>
	<div>
		<div class="hello there!"></div>
	</div>
	<div class="poo-class">poo</div>
</div>
', $containerControl->render());
		}

		protected function setUp() {
			$this->container = $container = ContainerFactory::create([
				ICacheStorage::class => new InMemoryCacheStorage(),
				ITemplateManager::class => TemplateManager::class,
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
				IRootDirectory::class => new RootDirectory(__DIR__),
				IAssetsDirectory::class => new AssetsDirectory(__DIR__ . '/../../../../../src/Edde/assets'),
				ICryptEngine::class => CryptEngine::class,
				'\SomeService\From\Container' => $this,
				IMacroSet::class => function (IContainer $container) {
					$macroSet = DefaultMacroSet::macroSet($container);
					$macroSet->onDeffered(function (IMacroSet $macroSet) use ($container) {
						$macroSet->registerMacro($container->inject(new HtmlMacro('custom-control', \AnotherCoolControl::class)));
					});
					return $macroSet;
				},
				IHelperSet::class => function (IContainer $container) {
					return DefaultMacroSet::helperSet($container);
				},
				ITempDirectory::class => function (IRootDirectory $rootDirectory) {
					return $rootDirectory->directory('temp', TempDirectory::class)
						->create();
				},
				ITemplateDirectory::class => function (ITempDirectory $tempDirectory) {
					return $tempDirectory->directory('.', TemplateDirectory::class);
				},
				IStyleSheetCompiler::class => ResourceList::class,
				IJavaScriptCompiler::class => ResourceList::class,
				IHostUrl::class => HostUrl::create('http://localhost/foo/bar?a=1'),
				IRequestUrl::class => function (IHostUrl $hostUrl) {
					return RequestUrl::create($hostUrl->getAbsoluteUrl());
				},
				IRequest::class => function (IRequestUrl $requestUrl) {
					return (new Request(''))->registerActionHandler('foo', 'bar', $requestUrl->getQuery());
				},
				ILinkFactory::class => function (IContainer $container, IHostUrl $hostUrl) {
					$linkFactory = new LinkFactory($hostUrl);
					$linkFactory->registerLinkGenerator($container->inject(new ControlLinkGenerator()));
					$linkFactory->registerLinkGenerator(new class extends AbstractLinkGenerator {
						/**
						 * @inheritdoc
						 */
						public function link($generate, ...$parameterList) {
							return $generate;
						}
					});
					return $linkFactory;
				},
				ITranslator::class => Translator::class,
			]);
			/** @var $converterManager IConverterManager */
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->inject(new XmlConverter()));
			$converterManager->registerConverter($container->inject(new CsvDictionaryConverter()));
			$this->templateManager = $container->create(ITemplateManager::class);
			$this->styleSheetList = $container->create(IStyleSheetCompiler::class);
			$this->javaScriptList = $container->create(IJavaScriptCompiler::class);
			$translator = $container->create(ITranslator::class);
			$translator->setLanguage('en');
		}
	}
