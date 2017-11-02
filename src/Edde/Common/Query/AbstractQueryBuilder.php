<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\Exception\QueryBuilderException;
		use Edde\Api\Query\Fragment\IFragment;
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
			 * @inheritdoc
			 */
			public function query(IQuery $query): INativeQuery {
				return $this->fragment($query);
			}

			/**
			 * @param IFragment $fragment
			 *
			 * @return INativeQuery
			 * @throws QueryBuilderException
			 */
			protected function fragment(IFragment $fragment): INativeQuery {
				if (isset($this->fragmentList[$name = $fragment->getType()]) === false) {
					throw new QueryBuilderException(sprintf('Unsupported fragment type [%s] in [%s].', $name, static::class));
				}
				return $this->fragmentList[$name]($fragment);
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
