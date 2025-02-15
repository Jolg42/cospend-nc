<?php

declare(strict_types=1);

namespace OCA\Cospend\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version000106Date20191023153118 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if ($schema->hasTable('cospend_projects')) {
			$table = $schema->getTable('cospend_projects');
			$table->addColumn('lastchanged', Types::INTEGER, [
				'notnull' => true,
				'default' => 0,
			]);
		}

		if ($schema->hasTable('cospend_bills')) {
			$table = $schema->getTable('cospend_bills');
			$table->addColumn('lastchanged', Types::INTEGER, [
				'notnull' => true,
				'default' => 0,
			]);
		}

		if ($schema->hasTable('cospend_members')) {
			$table = $schema->getTable('cospend_members');
			$table->addColumn('lastchanged', Types::INTEGER, [
				'notnull' => true,
				'default' => 0,
			]);
		}

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}
}
