<?php
	use Sami\Sami;

	return new Sami(__DIR__ . '/src/Edde', [
		'title'                => 'Edde Framework',
		'build_dir'            => __DIR__ . '/doc',
		'cache_dir'            => __DIR__ . '/temp/doc/cache',
		'default_opened_level' => 0,
	]);
