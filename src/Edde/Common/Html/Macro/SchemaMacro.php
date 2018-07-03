<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	/**
	 * Macro for simplier work with schemas.
	 */
	class SchemaMacro extends AbstractHtmlMacro {
		/**
		 * "If you were plowing a field, what would you rather use? 2 strong oxen or 1024 chickens?"
		 *
		 * - Seymour Cray
		 */
		public function __construct() {
			parent::__construct('schema');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->switchlude($macro, 'schema');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$stack = $compiler->getVariable(self::class . '/stack', $stack = new \SplStack());
			$schemaList = $compiler->getVariable(self::class . '/schema', []);
			$schema = explode(' ', $macro->getAttribute('schema'));
			if (isset($schema[1])) {
				$schemaList[$schema[1]] = $schema[0];
			}
			$compiler->setVariable(self::class . '/schema', $schemaList);
			$stack->push($schema[0]);
			parent::compile($macro, $compiler);
			$stack->pop();
		}
	}
