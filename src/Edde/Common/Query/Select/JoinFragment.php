<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Select;

	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;

	class JoinFragment extends AbstractFragment {
		/**
		 * @param string $source
		 * @param string|null $alias
		 *
		 * @return JoinOnFragment
		 */
		public function source($source, $alias = null) {
			$this->node->addNode($node = new Node('left-join', $source, [
				'alias' => $alias,
			]));
			return new JoinOnFragment($node);
		}
	}
