<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class AlphaNode extends Node {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof AlphaNode;
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class BetaNode extends Node {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof BetaNode;
		}
	}
