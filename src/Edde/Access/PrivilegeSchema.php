<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Schema\UuidSchema;

	/**
	 * Privilege is a group of permissions which could be built
	 * into hierarchical structure to make permission model.
	 *
	 * In general, privilege is built from individual permissions to
	 * work with some resource.
	 */
	interface PrivilegeSchema extends UuidSchema {
		public function name($unique): string;
	}
