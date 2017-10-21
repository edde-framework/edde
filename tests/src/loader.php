<?php
	declare(strict_types=1);
	namespace Edde;

		use Edde\Common\Autoloader;

		require_once __DIR__ . '/../../loader.php';
		Autoloader::register(__NAMESPACE__, __DIR__, false);
