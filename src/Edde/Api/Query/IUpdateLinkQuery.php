<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Schema\ILink;

		interface IUpdateLinkQuery extends IQuery {
			/**
			 * @param array $source
			 *
			 * @return IUpdateLinkQuery
			 */
			public function from(array $source): IUpdateLinkQuery;

			/**
			 * @param array $source
			 *
			 * @return IUpdateLinkQuery
			 */
			public function to(array $source): IUpdateLinkQuery;

			/**
			 * @return ILink
			 */
			public function getLink(): ILink;

			/**
			 * @return array
			 */
			public function getFrom(): array;

			/**
			 * @return array
			 */
			public function getTo(): array;
		}
