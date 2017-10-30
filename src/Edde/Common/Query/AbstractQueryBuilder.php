<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\INativeBatch;
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
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			public function build(IQuery $query): INativeBatch {
				return $this->fragment($query->getQuery());
			}

			/**
			 * @param INode $node
			 *
			 * @return INativeBatch
			 *
			 * @throws QueryBuilderException
			 */
			protected function fragment(INode $node): INativeBatch {
				if (isset($this->fragmentList[$name = $node->getName()]) === false) {
					throw new QueryBuilderException(sprintf('Unsupported fragment type [%s] in [%s].', $name, static::class));
				}
				return $this->fragmentList[$name]($node);
			}

			/**
			 * @param INode $root
			 *
			 * @return INativeBatch
			 * @throws QueryBuilderException
			 */
			protected function fragmentWhereList(INode $root): INativeBatch {
				$whereList = null;
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$query = $this->fragment($node);
					$where = null;
					if ($whereList && ($relationTo = $node->getAttribute('relation-to'))) {
						$where .= ' ' . strtoupper($relationTo) . "\n\t";
					}
					$where .= $query->getQuery();
					$whereList[] = $where . ' ' . strtoupper($node->getAttribute('relation'));
					$parameterList = array_merge($parameterList, $query->getParameterList());
				}
				return new NativeBatch("\t" . implode("\n\t", $whereList), $parameterList);
			}

			protected function fragmentParameterList(INode $root): INativeBatch {
				$parameterList = [];
				foreach ($root->getNodeList() as $node) {
					$parameterList[$node->getAttribute('name')] = $node->getValue();
				}
				return new NativeBatch('', $parameterList);
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
