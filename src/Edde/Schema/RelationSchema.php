<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface RelationSchema extends UuidSchema {
		const relation = true;
	}
