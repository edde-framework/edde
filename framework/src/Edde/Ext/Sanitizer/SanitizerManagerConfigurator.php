<?php
	declare(strict_types=1);
	namespace Edde\Ext\Sanitizer;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Sanitizer\ISanitizerManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Sanitizer\BoolSanitizer;
	use Edde\Common\Sanitizer\DateTimeSanitizer;
	use Edde\Common\Sanitizer\FloatSanitizer;

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
				'bool'           => $sanitizer = $this->container->create(BoolSanitizer::class, [], __METHOD__),
				'boolean'        => $sanitizer,
				'float'          => $sanitizer = $this->container->create(FloatSanitizer::class, [], __METHOD__),
				'double'         => $sanitizer,
				\DateTime::class => $sanitizer = $this->container->create(DateTimeSanitizer::class, [], __METHOD__),
				'datetime'       => $sanitizer,
			]);
		}
	}
