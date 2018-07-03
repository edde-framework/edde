<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * Schema property support.
	 */
	class PropertyMacro extends AbstractHtmlMacro {
		/**
		 * After many years of studying at a university, I’ve finally become a PhD… or Pizza Hut Deliveryman as people call it.
		 */
		public function __construct() {
			parent::__construct('property');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->insert($macro, 'property');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$property = explode('.', $this->attribute($macro, $compiler, 'property', false));

			$stack = $compiler->getVariable(SchemaMacro::class . '/stack', $stack = new \SplStack());
			$schemaList = $compiler->getVariable(SchemaMacro::class . '/schema', []);

			$schema = $stack->top();
			if (isset($property[1])) {
				if (isset($schemaList[$property[0]]) === false) {
					throw new MacroException(sprintf('Unknown schema name [%s] in macro [%s].', $property[1], $macro->getPath()));
				}
				$schema = $schemaList[$property[0]];
			}

			$node = $macro->getParent();
			$node->setAttribute('data-property', $property[0]);
			$node->setAttribute('data-schema', $schema);
		}
	}
