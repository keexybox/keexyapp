<?php
namespace App\Model\Table;

use App\Model\Entity\TrackingSession;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ActivesConnections Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Devices
 * @property \Cake\ORM\Association\BelongsTo $ProfilesBlacklists
 */
class ActivesConnectionsTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config)
	{
		$this->setTable('actives_connections');
		$this->setDisplayField('ip');
		$this->setPrimaryKey('ip');

		$this->belongsTo('Profiles', [
			'foreignKey' => 'profile_id',
			'joinType' => 'INNER'
		]);
		$this->belongsTo('Users', [
			'foreignKey' => 'user_id',
			'joinType' => 'LEFT'
		]);
		$this->belongsTo('Devices', [
			'foreignKey' => 'device_id',
			'joinType' => 'LEFT'
		]);
		$this->belongsTo('ProfilesBlacklists', [
			'foreignKey' => 'profile_id',
			'joinType' => 'LEFT'
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
			->requirePresence('ip')
			->notEmpty('ip')
			->add('ip', 'notEmpty', [
				'rule' => ['ip', 'ipv4'],
				'message' => __('Invalid IP address')
				]);
			
		$validator
			->add('ip', [
				'unique' => [
				'message' => __('This IP is already connected'),
				'provider' => 'table',
				'rule' => 'validateUnique'
				]
			]);
			
		$validator
			->requirePresence('name', 'create')
			->notEmpty('name');
			
		$validator
			->add('start_time', 'valid', ['rule' => 'numeric'])
			->requirePresence('start_time', 'create')
			->notEmpty('start_time');
			
		$validator
			->add('end_time', 'valid', ['rule' => 'numeric'])
			->requirePresence('end_time', 'create')
			->notEmpty('end_time');

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
