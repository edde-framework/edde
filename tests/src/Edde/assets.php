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

	interface ProjectSchema extends UuidSchema {
		const alias = true;

		public function name(): string;

		public function created($generator = 'stamp'): DateTime;

		public function start(): ?DateTime;

		public function end(): ?DateTime;
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

	interface VoidSchema extends UuidSchema {
	}
