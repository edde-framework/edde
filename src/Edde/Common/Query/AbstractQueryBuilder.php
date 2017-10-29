<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Object\Object;
		use ReflectionClass;
		use ReflectionMethod;

		abstract class AbstractQueryBuilder extends Object implements IQueryBuilder {
			use StringUtils;
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

			protected function fragmentParameterList(INode $root): INativeQuery {
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$parameterList[$node->getAttribute('name')] = $node->getValue();
				}
				return new NativeQuery('', $parameterList);
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				$reflectionClass = new ReflectionClass($this);
				foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED) as $reflectionMethod) {
					if (strpos($name = $reflectionMethod->getName(), 'fragment') === false || strlen($name) <= 8) {
						continue;
					}
					$this->fragmentList[$this->stringUtils->recamel(substr($name, 8))] = [
						$this,
						$name,
					];
				}
			}
		}