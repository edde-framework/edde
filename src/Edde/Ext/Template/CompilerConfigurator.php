<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Template\Macro\ForeachMacro;
	use Edde\Common\Template\Macro\HtmlMacro;
	use Edde\Common\Template\Macro\IfMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\LoadMacro;
	use Edde\Common\Template\Macro\NodeMacro;
	use Edde\Common\Template\Macro\SnippetMacro;
	use Edde\Common\Template\Macro\SwitchMacro;
	use Edde\Common\Template\Macro\TemplateMacro;
	use Edde\Common\Template\Macro\VirtualMacro;
	use Edde\Ext\Template\Macro\CssMacro;
	use Edde\Ext\Template\Macro\JsMacro;

	class CompilerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ICompiler $instance
		 */
		public function configure($instance) {
			$macroList = [
				SnippetMacro::class,
				IncludeMacro::class,
				LoadMacro::class,
				HtmlMacro::class,
				ForeachMacro::class,
				IfMacro::class,
				SwitchMacro::class,
				CssMacro::class,
				JsMacro::class,
				NodeMacro::class,
				VirtualMacro::class,
				TemplateMacro::class,
			];
			foreach ($macroList as $name) {
				/** @var $macro IMacro */
				$macro = $this->container->create($name, [], __METHOD__);
				$macro->register($instance);
			}
		}
	}
