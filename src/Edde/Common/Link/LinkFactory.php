<?php
	declare(strict_types=1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Link\LinkException;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Common\Deffered\AbstractDeffered;

	class LinkFactory extends AbstractDeffered implements ILinkFactory {
		use LazyLogServiceTrait;
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
		public function link($generate, ...$parameterList) {
			$this->use();
			$exception = null;
			foreach ($this->linkGeneratorList as $linkGenerator) {
				try {
					if (($url = $linkGenerator->link($generate, ...$parameterList)) !== null) {
						return $url;
					}
				} catch (\Exception $exception) {
					$this->logService->exception($exception, [
						'edde',
					]);
				}
			}
			throw new LinkException(sprintf('Cannot generate link from the given input%s.', (is_string($generate) ? ' [' . $generate . ']' : '')), 0, $exception);
		}
	}
