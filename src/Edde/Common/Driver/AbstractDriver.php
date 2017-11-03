<?php
	declare(strict_types=1);
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Object\Object;
		use ReflectionClass;
		use ReflectionMethod;

		abstract class AbstractDriver extends Object implements IDriver {
			/**
			 * @var callable[]
			 */
			protected $fragmentList;

			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
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
