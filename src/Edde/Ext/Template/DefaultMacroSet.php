<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Common\AbstractObject;
	use Edde\Common\Html\Helper\MethodHelper;
	use Edde\Common\Html\Helper\TranslateHelper;
	use Edde\Common\Html\Input\PasswordControl;
	use Edde\Common\Html\Input\TextControl;
	use Edde\Common\Html\Macro\AttrMacro;
	use Edde\Common\Html\Macro\ButtonMacro;
	use Edde\Common\Html\Macro\CallMacro;
	use Edde\Common\Html\Macro\CaseMacro;
	use Edde\Common\Html\Macro\ControlMacro;
	use Edde\Common\Html\Macro\CssMacro;
	use Edde\Common\Html\Macro\DictionaryMacro;
	use Edde\Common\Html\Macro\FillMacro;
	use Edde\Common\Html\Macro\HeaderMacro;
	use Edde\Common\Html\Macro\HtmlMacro;
	use Edde\Common\Html\Macro\IfMacro;
	use Edde\Common\Html\Macro\JsMacro;
	use Edde\Common\Html\Macro\LoadMacro;
	use Edde\Common\Html\Macro\LoopMacro;
	use Edde\Common\Html\Macro\PassChildMacro;
	use Edde\Common\Html\Macro\PassMacro;
	use Edde\Common\Html\Macro\PropertyMacro;
	use Edde\Common\Html\Macro\SchemaMacro;
	use Edde\Common\Html\Macro\SnippetMacro;
	use Edde\Common\Html\Macro\SwitchMacro;
	use Edde\Common\Html\Macro\TitleMacro;
	use Edde\Common\Html\Macro\TranslatorMacro;
	use Edde\Common\Html\Macro\UseMacro;
	use Edde\Common\Html\PlaceholderControl;
	use Edde\Common\Html\Tag\BlockquoteControl;
	use Edde\Common\Html\Tag\CaptionControl;
	use Edde\Common\Html\Tag\ColumnControl;
	use Edde\Common\Html\Tag\ColumnGroupControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\ImgControl;
	use Edde\Common\Html\Tag\ParagraphControl;
	use Edde\Common\Html\Tag\SectionControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Html\Tag\TableBodyControl;
	use Edde\Common\Html\Tag\TableCellControl;
	use Edde\Common\Html\Tag\TableControl;
	use Edde\Common\Html\Tag\TableFootControl;
	use Edde\Common\Html\Tag\TableHeadControl;
	use Edde\Common\Html\Tag\TableHeaderControl;
	use Edde\Common\Html\Tag\TableRowControl;
	use Edde\Common\Template\HelperSet;
	use Edde\Common\Template\Macro\BlockMacro;
	use Edde\Common\Template\Macro\ImportMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\MacroSet;

	/**
	 * Factory class for default macro and helper set creation.
	 */
	class DefaultMacroSet extends AbstractObject {
		/**
		 * cache method for default set of macros; they are created on demand (when requested macro list)
		 *
		 * @param IContainer $container
		 *
		 * @return IMacroSet
		 */
		static public function macroSet(IContainer $container): IMacroSet {
			$macroSet = new MacroSet();
			$macroSet->onDeffered(function (MacroSet $macroSet) use ($container) {
				$macroSet->setMacroList([
					$container->inject(new ImportMacro()),
					$container->inject(new LoadMacro()),
					$container->inject(new IncludeMacro()),
					$container->inject(new LoopMacro()),
					$container->inject(new IfMacro()),
					$container->inject(new SwitchMacro()),
					$container->inject(new CaseMacro()),
					$container->inject(new UseMacro()),
					$container->inject(new ControlMacro()),
					$container->inject(new BlockMacro()),
					$container->inject(new CallMacro()),
					$container->inject(new CssMacro()),
					$container->inject(new JsMacro()),
					$container->inject(new SchemaMacro()),
					$container->inject(new PropertyMacro()),
					$container->inject(new PassMacro()),
					$container->inject(new PassChildMacro()),
					$container->inject(new SnippetMacro()),
					$container->inject(new TranslatorMacro()),
					$container->inject(new DictionaryMacro()),
					$container->inject(new HtmlMacro('div', DivControl::class)),
					$container->inject(new HtmlMacro('span', SpanControl::class)),
					$container->inject(new HtmlMacro('p', ParagraphControl::class)),
					$container->inject(new HtmlMacro('img', ImgControl::class)),
					$container->inject(new HtmlMacro('text', TextControl::class)),
					$container->inject(new HtmlMacro('password', PasswordControl::class)),
					$container->inject(new HtmlMacro('placeholder', PlaceholderControl::class)),
					$container->inject(new HtmlMacro('table', TableControl::class)),
					$container->inject(new HtmlMacro('thead', TableHeadControl::class)),
					$container->inject(new HtmlMacro('tbody', TableBodyControl::class)),
					$container->inject(new HtmlMacro('tfoot', TableFootControl::class)),
					$container->inject(new HtmlMacro('td', TableCellControl::class)),
					$container->inject(new HtmlMacro('tr', TableRowControl::class)),
					$container->inject(new HtmlMacro('th', TableHeaderControl::class)),
					$container->inject(new HtmlMacro('caption', CaptionControl::class)),
					$container->inject(new HtmlMacro('col', ColumnControl::class)),
					$container->inject(new HtmlMacro('colgroup', ColumnGroupControl::class)),
					$container->inject(new HtmlMacro('blockquote', BlockquoteControl::class)),
					$container->inject(new HtmlMacro('section', SectionControl::class)),
					$container->inject(new HeaderMacro('h1')),
					$container->inject(new HeaderMacro('h2')),
					$container->inject(new HeaderMacro('h3')),
					$container->inject(new HeaderMacro('h4')),
					$container->inject(new HeaderMacro('h5')),
					$container->inject(new HeaderMacro('h6')),
					$container->inject(new ButtonMacro()),
					$container->inject(new FillMacro()),
					$container->inject(new TitleMacro()),
					$container->inject(new AttrMacro()),
				]);
			});
			return $macroSet;
		}

		/**
		 * cache method for default set of helpers; they are created on demand (when requested)
		 *
		 * @param IContainer $container
		 *
		 * @return IHelperSet
		 */
		static public function helperSet(IContainer $container): IHelperSet {
			$helperSet = new HelperSet();
			$helperSet->onDeffered(function (IHelperSet $helperSet) use ($container) {
				$helperSet->registerHelper($container->inject(new MethodHelper()));
				$helperSet->registerHelper($container->inject(new TranslateHelper()));
			});
			return $helperSet;
		}
	}
