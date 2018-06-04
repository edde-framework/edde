<?php
	declare(strict_types=1);
	use Edde\Schema\UuidSchema;

	interface UserSchema extends UuidSchema {
		public function login($unique = true): string;

		public function password(): string;
	}

	interface LabelSchema extends UuidSchema {
		public function name($unique): string;

		public function system($default = false): bool;
	}

	interface OrganizationSchema extends UuidSchema {
		const alias = true;

		public function name(): string;
	}

	interface ProjectSchema extends UuidSchema {
		const alias = true;
		const STATUS_CREATED = 0;
		const STATUS_STARTED = 1;
		const STATUS_ENDED = 2;
		const STATUS_ARCHIVED = 3;

		public function name(): string;

		public function status($default = self::STATUS_CREATED): int;

		public function created($generator = 'stamp'): DateTime;

		public function start(): ?DateTime;

		public function end(): ?DateTime;
	}

	interface ProjectOrganizationSchema extends UuidSchema {
		const alias = true;
		const relation = ['project' => 'organization'];

		public function project(): ProjectSchema;

		public function organization(): OrganizationSchema;
	}

	interface IssueSchema extends UuidSchema {
		const alias = true;

		public function name(): string;

		public function due(): ?DateTime;

		public function weight($default = 1.0): float;
	}

	interface IssueProjectSchema extends UuidSchema {
		public function issue(): IssueSchema;

		public function project(): ProjectSchema;
	}

	interface ProjectMemberSchema extends UuidSchema {
		const relation = ['project' => 'user'];
		const alias = true;

		public function project(): ProjectSchema;

		public function user(): UserSchema;

		public function owner($default = false): bool;
	}

	interface ProjectLabelSchema extends UuidSchema {
		const alias = true;

		public function project(): ProjectSchema;

		public function label(): LabelSchema;
	}

	interface IssueAssigneeSchema extends UuidSchema {
		public function issue(): IssueSchema;

		public function user(): UserSchema;

		public function created($generator = 'stamp'): DateTime;
	}

	interface ToBeOrdered extends UuidSchema {
		public function index(): float;
	}

	interface VoidSchema extends UuidSchema {
	}
