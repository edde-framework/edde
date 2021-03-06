<?php
declare(strict_types=1);

namespace Edde\Schema;

use DateTime;
use Edde\Service\Utils\StringUtils;
use ReflectionClass;
use ReflectionException;
use Throwable;
use function is_array;
use function is_string;
use function key;
use function reset;
use function str_replace;

class ReflectoinSchemaLoader extends AbstractSchemaLoader implements ISchemaLoader {
    use StringUtils;

    /** @inheritdoc */
    public function load(string $schema): ISchema {
        try {
            $reflectionClass = new ReflectionClass($schema);
            $schemaBuilder = new SchemaBuilder($schema);
            $primary = false;
            /** @var $source string */
            $source = null;
            /** @var $target string */
            $target = null;
            $relation = 0;
            foreach ($reflectionClass->getConstants() as $name => $value) {
                switch ($name) {
                    case 'alias':
                        /**
                         * auto alias support; kick off "schema" from the name and rest is used as an alias
                         */
                        if ($value === true) {
                            $value = str_replace('-schema', '', $this->stringUtils->recamel($this->stringUtils->extract($schema), '-'));
                        }
                        $schemaBuilder->alias($value);
                        break;
                    case 'primary':
                        $primary = $value;
                        break;
                    case 'relation':
                        [
                            $source,
                            $target,
                        ] = [
                            key($value),
                            reset($value),
                        ];
                        break;
                    case 'meta':
                        if (is_array($value) === false) {
                            throw new SchemaException(sprintf('Meta for schema [%s] must be an array.', $schema));
                        }
                        $schemaBuilder->meta($value);
                        break;
                }
            }
            if ($primary === false) {
                throw new SchemaException(sprintf('Schema [%s] has no primary property; please define one (you can extend [%s] to use default uuid support).', $schema, UuidSchema::class));
            }
            /**
             * go through all methods as they're used as schema definition
             */
            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $attributeBuilder = $schemaBuilder->property($propertyName = $reflectionMethod->getName());
                /**
                 * set default property type to a string
                 */
                $attributeBuilder->type($propertyType = 'string');
                if (($type = $reflectionMethod->getReturnType()) !== null) {
                    $attributeBuilder->type($propertyType = $type->getName());
                    $attributeBuilder->required($type->allowsNull() === false);
                }
                foreach ($reflectionMethod->getParameters() as $parameter) {
                    switch ($parameterName = $parameter->getName()) {
                        case 'unique':
                            $attributeBuilder->unique();
                            break;
                        case 'generator':
                            try {
                                $generator = $parameter->getDefaultValue();
                                if (is_string($generator) === false) {
                                    throw new ReflectionException('Generator name is not a string value.');
                                }
                            } catch (ReflectionException $exception) {
                                throw new SchemaException(sprintf('Parameter [%s::%s($generator)] must have a default string value as a generator name.', $schema, $propertyName), 0, $exception);
                            }
                            $attributeBuilder->filter($parameterName, $generator);
                            break;
                        case 'filter':
                            try {
                                $filter = $parameter->getDefaultValue();
                                if (is_string($filter) === false) {
                                    throw new ReflectionException('Filter name is not a string value.');
                                }
                            } catch (ReflectionException $exception) {
                                throw new SchemaException(sprintf('Parameter [%s::%s($filter)] must have a default string value as a filter name.', $schema, $propertyName), 0, $exception);
                            }
                            $attributeBuilder->filter($parameterName, $filter);
                            break;
                        case 'validator':
                            try {
                                $validator = $parameter->getDefaultValue();
                                if (is_string($validator) === false) {
                                    throw new ReflectionException('Validator name is not a string value.');
                                }
                            } catch (ReflectionException $exception) {
                                throw new SchemaException(sprintf('Parameter [%s::%s($validator)] must have a default string value as a validator name.', $schema, $propertyName), 0, $exception);
                            }
                            $attributeBuilder->validator($validator);
                            break;
                        case 'type':
                            try {
                                $type = $parameter->getDefaultValue();
                                if (is_string($type) === false) {
                                    throw new ReflectionException('Type name is not a string value.');
                                }
                            } catch (ReflectionException $exception) {
                                throw new SchemaException(sprintf('Parameter [%s::%s($type)] must have a default string value as a type name.', $schema, $propertyName), 0, $exception);
                            }
                            $attributeBuilder->type($type);
                            $attributeBuilder->required($parameter->isOptional());
                            $propertyType = $type;
                            break;
                        case 'default':
                            $attributeBuilder->default($parameter->getDefaultValue());
                            break;
                        case 'required':
                            $attributeBuilder->required($parameter->getDefaultValue());
                            break;
                        default:
                            throw new SchemaException(sprintf('Unknown schema [%s::%s] directive [%s].', $schema, $propertyName, $propertyName));
                    }
                }
                if ($propertyName === $primary) {
                    $primary = true;
                    $attributeBuilder->primary();
                    $attributeBuilder->filter('generator', $propertyName);
                } else if ($propertyName === $source || $propertyName === $target) {
                    $relation++;
                    $attributeBuilder->schema($propertyType);
                }
                switch ($propertyType) {
                    case 'float':
                    case 'int':
                    case 'bool':
                    case 'string':
                    case 'uuid':
                    case 'datetime':
                    case 'json':
                    case 'binary':
                    case DateTime::class:
                        $attributeBuilder->filter('type', $propertyType);
                        $attributeBuilder->validator($propertyType);
                        break;
                }
            }
            if ($source && $target && $relation !== 2) {
                throw new SchemaException(sprintf('Target [%s] or source [%s] property of relation is not present in schema [%s].', $source, $target, $schema));
            } else if ($source && $target && $relation === 2) {
                $schemaBuilder->relation($source, $target);
            }
            if ($primary !== true) {
                throw new SchemaException(sprintf('Primary property [%s::%s] is defined, but property does not exist; please add corresponding method to schema.', $schema, $primary));
            }
            return $schemaBuilder->create();
        } catch (SchemaException $exception) {
            throw $exception;
        } catch (Throwable $throwable) {
            throw new SchemaException(sprintf('Cannot do schema reflection of [%s]: %s', $schema, $throwable->getMessage()), 0, $throwable);
        }
    }
}
