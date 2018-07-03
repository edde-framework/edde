<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;
	use Edde\Common\Template\AbstractMacro;

	class JsMacro extends AbstractMacro {
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
				'minify-js',
				'js',
				'external-js',
			];
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-js':
					if ($this->minify) {
						throw new MacroException(sprintf('Js minify does not support recursion.'));
					}
					$this->minify = true;
					echo '<?php $this->javaScriptCompiler->clear(); ?>';
					break;
				case 'external-js':
					if ($this->external) {
						throw new MacroException(sprintf('Js external does not support recursion.'));
					}
					$this->external = true;
					break;
				case 'js':
					if ($this->minify) {
						echo '<?php $this->javaScriptCompiler->addResource($this->resourceProvider->getResource(' . $this->attribute($node, 'src') . ', $namespace, $context[null])); ?>';
						break;
					} else if ($this->external) {
						echo $this->htmlGenerator->generate(new Node('script', null, array_merge([
							'src'  => $this->attribute($node, 'src', true),
							'type' => 'text/javascript',
						], $node->getAttributeList()->array())));
						break;
					}
					echo '<?php $resource = $this->resourceProvider->getResource(' . $this->attribute($node, 'src') . ', $namespace, $context[null]); ?>';
					echo '<?php $resource = $this->assetStorage->store($resource); ?>';
					echo $this->htmlGenerator->generate(new Node('script', null, array_merge([
						'type' => 'text/javascript',
					], $node->getAttributeList()->array(), [
						'src' => function () {
							return '<?=$resource->getRelativePath()?>';
						},
					])));
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-js':
					$this->minify = false;
					echo $this->htmlGenerator->generate(new Node('script', null, [
						'src'  => function () {
							return '<?=$this->javaScriptCompiler->compile()->getRelativePath()?>';
						},
						'type' => 'text/javascript',
					]));
					break;
				case 'external':
					$this->external = false;
					break;
			}
		}
	}
