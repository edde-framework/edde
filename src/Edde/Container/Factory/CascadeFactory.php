<?php
	declare(strict_types=1);
	namespace Edde\Container\Factory;

	use Edde\Application\IContext;
	use Edde\Container\IContainer;

	class CascadeFactory extends AbstractDiscoveryFactory {
		/** @inheritdoc */
		protected function discover(IContainer $container, string $name): array {
			/** @var $context IContext */
			$context = $container->create(IContext::class, [], __METHOD__);
			return $context->cascade('\\', $name);
		}
	}
