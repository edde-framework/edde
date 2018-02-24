<?php
	declare(strict_types=1);
	use Sami\Sami;
	use Sami\Version\GitVersionCollection;

	$src = __DIR__ . '/src/Edde';
	return new Sami($src, [
		'title'                => 'Edde Framework',
		'versions'             => GitVersionCollection::create($src)
		                                              ->addFromTags('v*')
		                                              ->add('master', 'master'),
		'build_dir'            => __DIR__ . '/public/%version%',
		'cache_dir'            => __DIR__ . '/temp/%version%',
		'default_opened_level' => 1,
	]);
