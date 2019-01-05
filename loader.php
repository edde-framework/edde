<?php
	declare(strict_types=1);
	use Edde\Container\ContainerFactory;
	use Edde\Factory\CascadeFactory;
	use Edde\Factory\ClassFactory;

	require_once __DIR__ . '/lib/autoload.php';
	require_once __DIR__ . '/src/loader.php';
	return ContainerFactory::container([
		new CascadeFactory(
			[
				'Edde',
			]),
		/**
		 * This stranger here must (should be) be last, because it's canHandle method is able to kill a lot of dependencies and
		 * create not so much nice surprises. Thus, it must be last as kind of dependency fallback.
		 */
		new ClassFactory(),
	]);
