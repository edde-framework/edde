<?php
	declare(strict_types=1);
	use Edde\Access\AccessSchema;
	use Edde\Schema\UuidSchema;
	use Edde\User\UserSchema;

	interface ProjectSchema extends UuidSchema {
		public function name(): string;

		public function owner(): UserSchema;

		public function duration(): IntervalSchema;
	}

	interface IssueSchema extends UuidSchema {
		const alias = true;

		public function name(): string;

		public function due(): ?DateTime;

		public function weight($default = 1.0): float;

		public function reporter(): UserSchema;

		public function project(): ProjectSchema;
	}

	interface ProjectMemberSchema extends UuidSchema {
		const alias = true;

		public function project(): ProjectSchema;

		public function user(): UserSchema;

		public function owner($default = false): bool;
	}

	interface IssueAssigneeSchema extends UuidSchema {
		public function issue(): IssueSchema;

		public function user(): UserSchema;

		public function created($generator = 'stamp'): DateTime;
	}

	interface AccessProjectSchema extends UuidSchema {
		public function access(): AccessSchema;

		public function project(): ProjectSchema;
	}

	interface IntervalSchema extends UuidSchema {
		public function start(): ?DateTime;

		public function end(): ?DateTime;
	}

	interface VoidSchema extends UuidSchema {
	}
