<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Query;

	use Edde\Common\Query\AbstractStaticQueryFactory;

	/**
	 * Simple SQL query cache; it uses "" as delimiter and [] for quoting.
	 */
	class SqlQueryFactory extends AbstractStaticQueryFactory {
		/**
		 * @inheritdoc
		 */
		protected function delimite(string $delimite): string {
			return '"' . str_replace('"', '""', $delimite) . '"';
		}

		/**
		 * @inheritdoc
		 */
		protected function quote(string $quote): string {
			return "[$quote]";
		}

		/**
		 * @inheritdoc
		 */
		protected function type(string $type): string {
			return $type;
		}
	}
