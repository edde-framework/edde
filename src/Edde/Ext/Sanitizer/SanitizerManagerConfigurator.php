<?php
	declare(strict_types=1);
	namespace Edde\Ext\Sanitizer;

	use DateTime;
	use Edde\Api\Sanitizer\ISanitizerManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Sanitizer\BoolSanitizer;
	use Edde\Common\Sanitizer\DateTimeSanitizer;
	use Edde\Common\Sanitizer\FloatSanitizer;
	use Edde\Common\Sanitizer\IntSanitizer;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Inject\Container\Container;

	class SanitizerManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param ISanitizerManager $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerSanitizers([
				'bool'          => $sanitizer = $this->container->create(BoolSanitizer::class, [], __METHOD__),
				'boolean'       => $sanitizer,
				'int'           => $this->container->create(IntSanitizer::class, [], __METHOD__),
				'float'         => $sanitizer = $this->container->create(FloatSanitizer::class, [], __METHOD__),
				'double'        => $sanitizer,
				DateTime::class => $sanitizer = $this->container->create(DateTimeSanitizer::class, [], __METHOD__),
				'datetime'      => $sanitizer,
			]);
		}
	}
