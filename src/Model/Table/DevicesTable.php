<?php
namespace App\Model\Table;

use App\Model\Entity\Device;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Devices Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 *
 * @property \Cake\ORM\Association\hasMany $ConnectionsHistory
 */
class DevicesTable extends Table
{

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config)
	{
		$this->setTable('devices');
		$this->setDisplayField('devicename');
		$this->setPrimaryKey('id');

		$this->belongsTo('Profiles', [
			'foreignKey' => 'profile_id',
			'joinType' => 'INNER'
		]);

		$this->hasMany('ConnectionsHistory', [
			'dependent' => true,
			'foreingKey' => 'device_id'
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
			->add('id', 'valid', ['rule' => 'numeric'])
			->allowEmpty('id', 'create');
			
		$validator
			->requirePresence('devicename', 'create')
			->notEmpty('devicename');

		$validator
			->allowEmpty('profile_id', false)
			->allowEmpty('devicename', false);

		$validator
			->add('devicename', [
				'unique' => [
				'message' => __('Device name already used'),
				'provider' => 'table',
				'rule' => 'validateUnique'
				]
			]);

		$validator
			->add('devicename', 'notEmpty', [
			'rule' => ['custom', "/(?=^[a-zA-Z0-9_\-]*$)/i"],
			'message' => __('The value must be alphanumeric and may contain hyphens (-) and underscores (_)')
		]);
		
		$validator
			->requirePresence('mac', 'create')
			->notEmpty('mac');
			
		$validator
			->add('mac', [
				'unique' => [
				'message' => __('This name is already taken'),
				'provider' => 'table',
				'rule' => 'validateUnique'
				]
		]);

		$validator
			->requirePresence('mac', 'create')
			->notEmpty('mac', __('The value should not be empty'))
			->add('mac', 'notEmpty', [
			// Allow only MAC address like XX:XX:XX:XX:XX:XX
			'rule' => ['custom', "/(?=^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$)/i"],
			'message' => __('Invalid MAC address')
		]);

		$validator
			->allowEmpty('dhcp_reservation_ip');

        $validator
			->add('dhcp_reservation_ip', 'ipv4', [
				'rule' => ['ip', 'ipv4'],
				'message' => __('Invalid IP address')
			]);

		$validator
			->add('dhcp_reservation_ip', [
				'unique' => [
				'message' => __('This IP address is already reserved'),
				'provider' => 'table',
				'rule' => 'validateUnique'
				]
			]);

		return $validator;
	}

	/**
	 * Returns a rules checker object that will be used for validating
	 * application integrity.
	 *
	 * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
	 * @return \Cake\ORM\RulesChecker
	 */
	public function buildRules(RulesChecker $rules)
	{
		$rules->add($rules->existsIn(['profile_id'], 'Profiles'));
		return $rules;
	}
}
