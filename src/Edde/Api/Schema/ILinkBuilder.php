<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface ILinkBuilder {
			/**
			 * return link name
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * return source schema name
			 *
			 * @return string
			 */
			public function getSourceSchema(): string;

			/**
			 * return source schema property name
			 *
			 * @return string
			 */
			public function getSourceProperty(): string;

			/**
			 * return target schema name
			 *
			 * @return string
			 */
			public function getTargetSchema(): string;

			/**
			 * return target schema property name
			 *
			 * @return string
			 */
			public function getTargetProperty(): string;
		}
