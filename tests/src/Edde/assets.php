<?php
	declare(strict_types=1);
	use Edde\Schema\UuidSchema;

	interface LabelSchema extends UuidSchema {
		public function name($unique): string;

		public function system($default = false): bool;
	}

	interface ProjectSchema extends UuidSchema {
		public function name(): string;

		public function start(): ?DateTime;

		public function end(): ?DateTime;
	}

	interface IssueSchema extends UuidSchema {
		const alias = true;

		public function name(): string;

		public function due(): ?DateTime;

		public function weight($default = 1.0): float;

		public function project(): ProjectSchema;
	}

	interface ProjectMemberSchema extends UuidSchema {
		const alias = true;

		public function project(): ProjectSchema;

		public function owner($default = false): bool;
	}

	interface IssueAssigneeSchema extends UuidSchema {
		public function issue(): IssueSchema;

		public function created($generator = 'stamp'): DateTime;
	}

	interface DataSchema extends UuidSchema {
		public function json($type = 'json');

		public function php($type = 'php');
	}

	interface VoidSchema extends UuidSchema {
	}
