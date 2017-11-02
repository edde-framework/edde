<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IFragment;
		use Edde\Common\Object\Object;

		abstract class AbstractFragment extends Object implements IFragment {
			/**
			 * @var string
			 */
			protected $type;

			public function __construct(string $type) {
				$this->type = $type;
			}

			/**
			 * @inheritdoc
			 */
			public function getType(): string {
				return $this->type;
			}
		}
