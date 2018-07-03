<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;
	use Edde\Common\Template\AbstractMacro;

	class CssMacro extends AbstractMacro {
		use LazyHtmlGeneratorTrait;
		/**
		 * @var bool
		 */
		protected $minify = false;
		protected $external = false;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return [
				'minify-css',
				'css',
				'external-css',
			];
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-css':
					if ($this->minify) {
						throw new MacroException(sprintf('Css minify does not support recursion.'));
					}
					$this->minify = true;
					echo '<?php $this->styleSheetCompiler->clear(); ?>';
					break;
				case 'external-css':
					if ($this->external) {
						throw new MacroException(sprintf('Css external does not support recursion.'));
					}
					$this->external = true;
					break;
				case 'css':
					if ($this->minify) {
						echo '<?php $this->styleSheetCompiler->addResource($this->resourceProvider->getResource(' . $this->attribute($node, 'src') . ', $namespace, $context[null])); ?>';
						break;
					} else if ($this->external) {
						echo $this->htmlGenerator->generate(new Node('link', null, array_merge([
							'href' => $this->attribute($node, 'src', true),
							'rel'  => 'stylesheet',
							'type' => 'text/css',
						], $node->getAttributeList()->array())));
						break;
					}
					echo '<?php $resource = $this->resourceProvider->getResource(' . $this->attribute($node, 'src') . ', $namespace, $context[null]); ?>';
					echo '<?php $resource = $this->assetStorage->store($resource); ?>';
					echo $this->htmlGenerator->generate(new Node('link', null, array_merge([
						'href' => function () {
							return '<?=$resource->getRelativePath();?>';
						},
						'rel'  => 'stylesheet',
						'type' => 'text/css',
					], $node->getAttributeList()->array())));
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-css':
					$this->minify = false;
					echo $this->htmlGenerator->generate(new Node('link', null, [
						'href' => function () {
							return '<?=$this->styleSheetCompiler->compile()->getRelativePath()?>';
						},
						'rel'  => 'stylesheet',
						'type' => 'text/css',
					]));
					break;
				case 'external':
					$this->external = false;
					break;
			}
		}
	}
