<?php
	declare(strict_types=1);

	namespace Edde\Api\Html;

	use Edde\Api\Node\INode;

	interface IHtmlGenerator {
		/**
		 * return set of supported tags
		 *
		 * @return array
		 */
		public function getTagList(): array;

		/**
		 * renders (echoes) output directly
		 *
		 * @param INode $root
		 *
		 * @return IHtmlGenerator
		 */
		public function render(INode $root): IHtmlGenerator;

		/**
		 * try to generate a html output from the given node
		 *
		 * @param INode $root
		 *
		 * @return string
		 */
		public function generate(INode $root): string;

		/**
		 * generate open tag only for the given node
		 *
		 * @param INode    $node
		 * @param int|null $level explicitly export html element level
		 *
		 * @return string
		 */
		public function open(INode $node, int $level = null): string;

		/**
		 * node content renering
		 *
		 * @param INode $node
		 *
		 * @return string
		 */
		public function content(INode $node): string;

		/**
		 * generate close tag only for the given node
		 *
		 * @param INode    $node
		 * @param int|null $level
		 *
		 * @return string
		 */
		public function close(INode $node, int $level = null): string;
	}
