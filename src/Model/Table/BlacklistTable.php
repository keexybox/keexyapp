<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Blacklist Model
 * This model uses a different database than the default one
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 */
class BlacklistTable extends Table
{
	/**
	 * Define another default connection.
	 *
	 * @return Database connection to use
	 */
	public static function defaultConnectionName()
	{
		return 'keexyboxblacklist';
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator)
	{
		$validator
			->add('category', 'name', [
				'rule' => ['custom', "/(?=^[a-zA-Z0-9_\-]*$)/i"],
				'message' => __('The value must be alphanumeric and may contain hyphens (-) and underscores (_)')
			]);

		$validator
			->add('zone', 'fqdn', [
				// Regex for FQDN
				'rule' => ['custom', "/(?=^.{4,253}$)(^((?!-)[a-zA-Z0-9-]{1,63}(?<!-)\.)+[a-zA-Z]{2,63}$)/i"],
				'message' => __('Invalid domain')
			]);

		//$validator->add('host', 'notEmpty');
		return $validator;
	}
}
