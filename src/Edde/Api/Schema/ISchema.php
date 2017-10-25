<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Node\INode;
		use Edde\Api\Schema\Exception\UnknownPropertyException;

		interface ISchema extends IConfigurable {
			/**
			 * return name of a schema (it could have even "namespace" like name)
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * set alias to this schema; later in queries name or alias could be used
			 *
			 * @param string $alias
			 *
			 * @return ISchema
			 */
			public function alias(string $alias): ISchema;

			/**
			 * has this schema alias?
			 *
			 * @return bool
			 */
			public function hasAlias(): bool;

			/**
			 * return schema alias
			 *
			 * @return string|null
			 */
			public function getAlias(): ?string;

			/**
			 * @param string $name
			 *
			 * @return IProperty
			 *
			 * @throws UnknownPropertyException
			 */
			public function getProperty(string $name): IProperty;

			/**
			 * return list of properties of this schema
			 *
			 * @return IProperty[]
			 */
			public function getPropertyList(): array;

			/**
			 * return an array with all primary properties
			 *
			 * @return IProperty[]
			 */
			public function getPrimaryList(): array;

			/**
			 * return a primary property of this schema (should throw an exception
			 * when there are more primary properties)
			 *
			 * @return IProperty
			 */
			public function getPrimary(): IProperty;

			/**
			 * return an array with properties which are link
			 *
			 * @return IProperty[]
			 */
			public function getLinkList(): array;

			/**
			 * get link by a target schema
			 *
			 * @param string $target
			 *
			 * @return ILink
			 */
			public function getLink(string $target): ILink;

			/**
			 * return list of property nodes
			 *
			 * @return INode[]
			 */
			public function getNodeList(): array;

			/**
			 * create a new property with the given name
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function property(string $name): IProperty;

			/**
			 * create a primary propery on the schema
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function primary(string $name): IProperty;

			/**
			 * create a string type property with the given name
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function string(string $name): IProperty;

			/**
			 * create a text type property (should be unlimited text field)
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function text(string $name): IProperty;

			/**
			 * create a common integer property
			 *
			 * @param string $name
			 *
			 * @return IProperty
			 */
			public function integer(string $name): IProperty;
		}
