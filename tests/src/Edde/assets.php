<?php
	declare(strict_types=1);
	use Edde\Edde;
	use Edde\Schema\UuidSchema;

	class ShittyInjectClass extends Edde {
		public function injectSomething(UserSchema $userSchema) {
		}
	}

	class ShittyInjectVisibilityClass extends Edde {
		protected function injectSomething(UserSchema $userSchema) {
		}
	}

	class ShittyInjectTypehintClass extends Edde {
		protected $userSchema;

		public function injectSomething($userSchema) {
		}
	}

	class ConstructorClass {
		public $param;

		/**
		 * @param $param
		 */
		public function __construct($param) {
			$this->param = $param;
		}
	}

	interface UserSchema extends UuidSchema {
		public function login($unique = true): string;

		public function password(): string;
	}

	interface LabelSchema extends UuidSchema {
		const alias = true;

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
		const relation = ['issue' => 'project'];

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
		const relation = ['project' => 'label'];
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

	interface ShittyTypeSchema extends UuidSchema {
		public function item($type = 'this-type-does-not-exists');
	}

	interface VoidSchema extends UuidSchema {
	}
