<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Common\Filter\GuidFilter;

	return [
		'name' => 'IdentityStorable',
		'namespace' => __NAMESPACE__,
		'property-list' => [
			[
				'name' => 'guid',
				'unique' => true,
				'identifier' => true,
				'generator' => GuidFilter::class,
			],
			[
				'name' => 'name',
			],
			[
				'name' => 'login',
				'unique' => true,
			],
			[
				'name' => 'hash',
				'comment' => 'password, ... ',
			],
			[
				'name' => 'token',
				'unique' => true,
				'required' => false,
				'comment' => "this can be used for some sort of 'private' access; ex. API",
			],
		],
		'meta-list' => [
			'edde' => true,
			'storable' => true,
		],
	];
