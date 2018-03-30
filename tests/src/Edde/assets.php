<?php
	declare(strict_types=1);
	use Edde\Schema\AliasSchema;
	use Edde\Schema\RelationSchema;
	use Edde\Schema\UuidSchema;

	interface PrivilegeSchema extends UuidSchema, AliasSchema {
		public function name($unique): string;
	}

	interface PermissionSchema extends UuidSchema, AliasSchema {
		public function name($unique): string;
	}

	interface PrivilegePermissionSchema extends RelationSchema, AliasSchema {
		public function privilege(): PrivilegeSchema;

		public function permission(): PermissionSchema;
	}

	interface UserSchema extends UuidSchema, AliasSchema {
		public function name($unique): string;

		public function email($unique): string;

		public function password(): string;

		public function nick(): ?string;

		public function created($generator = 'stamp'): DateTime;
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
		public function user(): UserSchema;

		public function project(): ProjectSchema;
	}

	interface IssueAssigneeSchema extends RelationSchema {
		public function issue(): IssueSchema;

		public function user(): UserSchema;

		public function created($generator = 'stamp'): DateTime;
	}

	interface IntervalSchema extends UuidSchema {
		public function start(): ?DateTime;

		public function end(): ?DateTime;
	}

	interface VoidSchema extends UuidSchema {
	}
