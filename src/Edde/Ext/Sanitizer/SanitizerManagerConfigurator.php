<?php
	namespace Edde\Ext\Sanitizer;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Sanitizer\ISanitizerManager;
		use Edde\Common\Config\AbstractConfigurator;
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
				$instance->registerSanitizerList([
					'float' => $this->container->create(FloatSanitizer::class, [], __METHOD__),
				]);
			}
		}
