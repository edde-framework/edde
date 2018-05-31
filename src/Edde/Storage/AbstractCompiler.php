<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Edde;
	use Edde\Query\QueryException;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;

	abstract class AbstractCompiler extends Edde implements ICompiler {
		/**
		 * @param ISchema $relationSchema
		 * @param ISchema $entitySchema
		 * @param ISchema $targetSchema
		 *
		 * @throws QueryException
		 * @throws SchemaException
		 */
		protected function checkRelation(ISchema $relationSchema, ISchema $entitySchema, ISchema $targetSchema): void {
			$sourceAttribute = $relationSchema->getSource();
			$targetAttribute = $relationSchema->getTarget();
			if ($relationSchema->isRelation() === false) {
				throw new QueryException(sprintf('Cannot attach [%s] to [%s] because relation [%s] is not relation.', $entitySchema->getName(), $targetSchema->getName(), $relationSchema->getName()));
			} else if (($expectedSchemaName = $sourceAttribute->getSchema()) !== ($schemaName = $entitySchema->getName())) {
				throw new QueryException(sprintf('Source schema [%s] of entity differs from expected relation [%s] source schema [%s]; did you swap source ($entity) and $target?.', $schemaName, $relationSchema->getName(), $expectedSchemaName));
			} else if (($expectedSchemaName = $targetAttribute->getSchema()) !== ($schemaName = $targetSchema->getName())) {
				throw new QueryException(sprintf('Target schema [%s] of entity differs from expected relation [%s] source schema [%s]; did you swap source ($entity) and $target?.', $schemaName, $relationSchema->getName(), $expectedSchemaName));
			}
		}
	}
