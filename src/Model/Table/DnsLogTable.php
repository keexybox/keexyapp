<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DnsLog Model
 * This model uses a different database than the default one
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 */
class DnsLogTable extends Table
{
	/**
	 * Define another default connection.
	 *
	 * @return Database connection to use
	 */
	public static function defaultConnectionName()
    {
        return 'keexyboxlogs';
    }

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config)
	{
		$this->belongsTo('KeexyboxProfiles', [
			//'foreignKey' => 'profile_id',
			'className' => 'DnsLog',
			'foreignKey' => false,
			'joinType' => 'INNER',
			'conditions' => ['keexybox_profiles.id' => 'dns_log.profile_id']
		]);
	}
}
