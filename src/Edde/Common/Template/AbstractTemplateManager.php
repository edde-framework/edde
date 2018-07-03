<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Object;

	abstract class AbstractTemplateManager extends Object implements ITemplateManager {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 */
		public function template(): ITemplate {
			return $this->container->create(ITemplate::class);
		}
	}
