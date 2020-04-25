<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KeexyboxProfiles Model
 * This model points to a table view
 * This model uses a different database than the default one
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 */
class KeexyboxProfilesTable extends Table
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
		$this->setDisplayField('profilename');

		$this->hasMany('DnsLog', [
			'className' => 'KeexyboxProfiles',
			'foreingKey' => false,
			'conditions' => ['dns_log.profile_id' => 'keexybox_profiles.id']
		]);
	}
}
