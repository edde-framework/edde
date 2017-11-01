<?php
	declare(strict_types=1);
	namespace Edde\Common\Generator;

		use Edde\Api\Crypt\Inject\RandomService;

		class GuidGenerator extends AbstractGenerator {
			use RandomService;

			/**
			 * @inheritdoc
			 */
			public function generate(array $options = []) {
				return $this->randomService->guid();
			}
		}
