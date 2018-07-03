<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Upgrade;

	use Edde\Common\Filter\GuidFilter;

	return [
		'name' => 'UpgradeStorable',
		'namespace' => __NAMESPACE__,
		'property-list' => [
			[
				'name' => 'guid',
				'identifier' => true,
				'unique' => true,
				'required' => true,
				'generator' => GuidFilter::class,
			],
			[
				'name' => 'version',
				'unique' => true,
				'required' => true,
			],
			[
				'name' => 'stamp',
				'type' => 'float',
				'required' => true,
			],
		],
		'meta-list' => [
			'edde' => true,
			'storable' => true,
		],
	];
