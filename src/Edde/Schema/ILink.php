<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface ILink {
		/**
		 * return name of a link
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * get a from source schema/property
		 *
		 * @return ITarget
		 */
		public function getFrom(): ITarget;

		/**
		 * get a target target schema/property
		 *
		 * @return ITarget
		 */
		public function getTo(): ITarget;
	}
