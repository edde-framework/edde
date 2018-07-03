<?php
	declare(strict_types=1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Link\LinkException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	class LinkFactory extends Object implements ILinkFactory {
		use ConfigurableTrait;
		/**
		 * @var ILinkGenerator[]
		 */
		protected $linkGeneratorList = [];

		/**
		 * @inheritdoc
		 */
		public function registerLinkGenerator(ILinkGenerator $linkGenerator): ILinkFactory {
			$this->linkGeneratorList[] = $linkGenerator;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws LinkException
		 */
		public function link($generate, array $parameterList = []) {
			foreach ($this->linkGeneratorList as $linkGenerator) {
				$linkGenerator->setup();
				if (($url = $linkGenerator->link($generate, $parameterList)) !== null) {
					return $url;
				}
			}
			throw new LinkException(sprintf('Cannot generate link from the given input%s.', (is_string($generate) ? ' [' . $generate . ']' : '')));
		}
	}
