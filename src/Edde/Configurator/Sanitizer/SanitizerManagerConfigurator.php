<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Sanitizer;

	use DateTime;
	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Inject\Container\Container;
	use Edde\Sanitizer\BoolSanitizer;
	use Edde\Sanitizer\DateTimeSanitizer;
	use Edde\Sanitizer\FloatSanitizer;
	use Edde\Sanitizer\IntSanitizer;
	use Edde\Sanitizer\ISanitizerManager;
	use Edde\Sanitizer\JsonSanitizer;

	class SanitizerManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param ISanitizerManager $instance
		 *
		 * @throws ContainerException
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
				'json'          => $this->container->create(JsonSanitizer::class, [], __METHOD__),
			]);
		}
	}
