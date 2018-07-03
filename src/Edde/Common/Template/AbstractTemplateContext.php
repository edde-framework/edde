<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\ITemplateContext;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractTemplateContext extends Object implements ITemplateContext {
		use ConfigurableTrait;
	}
