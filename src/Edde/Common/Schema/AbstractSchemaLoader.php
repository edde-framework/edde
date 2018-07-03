<?php
	declare(strict_types=1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchemaLoader;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object\Object;

	abstract class AbstractSchemaLoader extends Object implements ISchemaLoader {
		use ConfigurableTrait;
	}
