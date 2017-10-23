<?php
	namespace Edde\Common\Filter;

		use Edde\Api\Crypt\Inject\RandomService;

		class GuidFilter extends AbstractFilter {
			use RandomService;

			/**
			 * @inheritdoc
			 */
			public function filter($value, array $options = []) {
				return $this->randomService->guid($value);
			}
		}
