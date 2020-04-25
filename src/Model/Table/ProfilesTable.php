<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Profiles Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\hasMany $Users
 * @property \Cake\ORM\Association\hasMany $Devices
 * @property \Cake\ORM\Association\hasMany $ActivesConnections
 * @property \Cake\ORM\Association\hasMany $ProfilesTimes
 * @property \Cake\ORM\Association\hasMany $ProfilesRouting
 * @property \Cake\ORM\Association\hasMany $ProfilesIpfilters
 * @property \Cake\ORM\Association\hasMany $ConnectionsHistory
 * @property \Cake\ORM\Association\hasMany $ProfilesBlacklists
 * @property \Cake\ORM\Association\hasMany $Users
 * @property \Cake\ORM\Association\hasMany $Users
 * @property \Cake\ORM\Association\hasMany $Users
 */
class ProfilesTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config)
	{
		// Field to show in Form select or for something else
		$this->setDisplayField('profilename');
		// A profile has many users

		$this->hasMany('Users', [
			'foreingKey' => 'profile_id'
		]);

		$this->hasMany('Devices', [
			'foreingKey' => 'profile_id'
		]);

		$this->hasMany('ActivesConnections', [
			'foreingKey' => 'profile_id'
		]);

		// dependent allow cascading deletion when profile is removed

		$this->hasMany('ProfilesTimes', [
			'dependent' => true,
			'foreingKey' => 'profile_id'
		]);

		$this->hasMany('ProfilesRouting', [
			'dependent' => true,
			'foreingKey' => 'profile_id'
		]);

		$this->hasMany('ProfilesIpfilters', [
			'dependent' => true,
			'foreingKey' => 'profile_id'
		]);

		$this->hasMany('ConnectionsHistory', [
			'dependent' => true,
			'foreingKey' => 'profile_id'
		]);

		$this->hasMany('ProfilesBlacklists', [
			'dependent' => true,
			'foreingKey' => 'profile_id'
		]);
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
			->allowEmpty('profilename', false)
			->notEmpty('default_routing', 'Default Websites access must be define')
			->notEmpty('default_ipfilter', 'Default firewall rule must be define');

		$validator
			->add('profilename', [
				'unique' => [
				'message' => __('This name is already taken'),
				'provider' => 'table',
				'rule' => 'validateUnique'
				]
			]);

		return $validator;
	}
}

