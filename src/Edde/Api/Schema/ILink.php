<?php
	namespace Edde\Api\Schema;

		interface ILink {
			/**
			 * get target schema of the link
			 *
			 * @return string
			 */
			public function getTarget(): string;

			/**
			 * return targeted property in the schema (or null to use primary property)
			 *
			 * @return null|string
			 */
			public function getProperty(): ?string;
		}
