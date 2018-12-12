<?php
	declare(strict_types=1);
	namespace Edde;

	require_once __DIR__ . '/../../loader.php';
	require_once __DIR__ . '/Edde/assets.php';
	require_once __DIR__ . '/Edde/Application/assets.php';
	require_once __DIR__ . '/Edde/Container/assets.php';
	require_once __DIR__ . '/Edde/Filter/assets.php';
	require_once __DIR__ . '/Edde/Message/assets.php';
	Autoloader::register(__NAMESPACE__, __DIR__, false);
