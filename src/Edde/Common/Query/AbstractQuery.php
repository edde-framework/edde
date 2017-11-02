<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\IQuery;
		use Edde\Common\Query\Fragment\AbstractFragment;

		abstract class AbstractQuery extends AbstractFragment implements IQuery {
			/**
			 * @inheritdoc
			 */
			public function getParameterList(): array {
				return [];
			}

			/**
			 * @inheritdoc
			 */
			public function getDescription(): ?string {
				return static::class;
			}
		}
