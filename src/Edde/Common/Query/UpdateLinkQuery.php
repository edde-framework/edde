<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\IUpdateLinkQuery;
		use Edde\Api\Schema\ILink;

		/**
		 * General relation query (1:n, m:n).
		 */
		class UpdateLinkQuery extends AbstractQuery implements IUpdateLinkQuery {
			/**
			 * @var ILink
			 */
			protected $link;
			/**
			 * @var array
			 */
			protected $from;
			/**
			 * @var array
			 */
			protected $to;

			public function __construct(ILink $link) {
				$this->link = $link;
			}

			/**
			 * @inheritdoc
			 */
			public function from(array $source): IUpdateLinkQuery {
				$this->from = $source;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function to(array $source): IUpdateLinkQuery {
				$this->to = $source;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getLink(): ILink {
				return $this->link;
			}

			/**
			 * @inheritdoc
			 */
			public function getFrom(): array {
				return $this->from;
			}

			/**
			 * @inheritdoc
			 */
			public function getTo(): array {
				return $this->to;
			}
		}
