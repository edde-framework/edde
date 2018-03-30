<?php
	declare(strict_types=1);
	use Edde\Schema\AliasSchema;
	use Edde\Schema\RelationSchema;
	use Edde\Schema\UuidSchema;

	interface PermissionSchema extends UuidSchema, AliasSchema {
		public function name($unique): string;
	}

	interface RoleSchema extends UuidSchema, AliasSchema {
		public function name($unique): string;
	}

	interface AccessSchema extends UuidSchema, AliasSchema {
	}

	interface UserSchema extends UuidSchema, AliasSchema {
		public function name($unique): string;

		public function email($unique): string;

		public function password(): string;

		public function nick(): ?string;

		public function created($generator = 'stamp'): DateTime;
	}

	interface PermissionRoleSchema extends RelationSchema {
		public function permission(): PermissionSchema;

		public function role(): RoleSchema;
	}

	interface PermissionUserSchema extends RelationSchema, AliasSchema {
		public function permission(): PermissionSchema;

		public function user(): UserSchema;
	}

	interface UserRoleSchema extends RelationSchema {
		public function user(): UserSchema;

		public function role(): RoleSchema;
	}

	interface UserPermissionSchema extends RelationSchema {
		public function user(): UserSchema;

		public function permission(): PermissionSchema;
	}

	interface UserAccessSchema extends RelationSchema {
		public function user(): UserSchema;

		public function access(): AccessSchema;
	}

	interface AccessRoleSchema extends RelationSchema, AliasSchema {
		public function access(): AccessSchema;

		public function role(): RoleSchema;
	}

	interface AccessPermissionSchema extends RelationSchema {
		public function access(): AccessSchema;

		public function permission(): PermissionSchema;
	}

	interface ProjectSchema extends UuidSchema {
		public function name(): string;

		public function owner(): UserSchema;

		public function duration(): IntervalSchema;
	}

	interface IssueSchema extends UuidSchema, AliasSchema {
		public function name(): string;

		public function due(): ?DateTime;

		public function weight($default = 1.0): float;

		public function reporter(): UserSchema;

		public function project(): ProjectSchema;
	}

	interface ProjectMemberSchema extends RelationSchema, AliasSchema {
		public function project(): ProjectSchema;

		public function user(): UserSchema;
	}

	interface IssueAssigneeSchema extends RelationSchema {
		public function issue(): IssueSchema;

		public function user(): UserSchema;

		public function created($generator = 'stamp'): DateTime;
	}

	interface AccessProjectSchema extends RelationSchema {
		public function access(): AccessSchema;

		public function project(): ProjectSchema;
	}

	interface IntervalSchema extends UuidSchema {
		public function start(): ?DateTime;

		public function end(): ?DateTime;
	}

	interface VoidSchema extends UuidSchema {
	}
