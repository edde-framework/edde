<?php
	declare(strict_types=1);
	use Edde\Application\IApplication;
	use Edde\Container\IContainer;

	// loader should create container instance (without any side effects)
	/** @var $container IContainer */
	$container = require_once __DIR__ . '/loader.php';
	// Edde specifies simple interface for an application lifecycle; exit is here to
	// report exit status of CLI applications (http don't care)
	exit($container->create(IApplication::class)->run());
