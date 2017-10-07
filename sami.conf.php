<?php
	use Sami\Sami;

	return new Sami($src = (__DIR__ . '/src'), [
		'title'                => 'Edde Framework',
		'build_dir'            => __DIR__ . '/doc',
		'cache_dir'            => __DIR__ . '/temp/doc/cache',
		'default_opened_level' => 2,
	]);
