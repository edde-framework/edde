<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Common\Object\Object;

		abstract class AbstractQueryBuilder extends Object implements IQueryBuilder {
			protected $fragmentList = [];

			/**
			 * @param IQuery $query
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			public function build(IQuery $query): INativeQuery {
				return $this->fragment($query->getQuery());
			}

			/**
			 * @param INode $node
			 *
			 * @return INativeQuery
			 *
			 * @throws QueryBuilderException
			 */
			protected function fragment(INode $node): INativeQuery {
				if (isset($this->fragmentList[$name = $node->getName()]) === false) {
					throw new QueryBuilderException(sprintf('Unsupported fragment type [%s] in [%s].', $name, static::class));
				}
				return $this->fragmentList[$name]($node);
			}
		}
