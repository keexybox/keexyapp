<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NoHashPasswordUsers Model
 * This model is the same as Users Model, but it does not have a hash password validation
 * This Model is only use by update already Hashed password from CSV file
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 *
 * @property \Cake\ORM\Association\hasMany $ConnectionsHistory
 */
class UsersNoHashPasswordTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config)
	{
		// Load table that has different name as Model
		$this->setTable('users');
		// After Cakephp 3.4.0
		//$this->setTable('users');

		$this->setDisplayField('username');

		$this->belongsTo('Profiles', [
			'foreignKey' => 'profile_id',
		]);

		$this->hasMany('ConnectionsHistory', [
			'dependent' => true,
			'foreingKey' => 'user_id'
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
				->add('username', 'notEmpty', [
				'rule' => ['custom', "/(?=^[a-zA-Z0-9_\-]*$)/i"],
				'message' => __('The value must be alphanumeric and may contain hyphens (-) and underscores (_)')
			]);

		$validator
			->allowEmpty('profile_id', false)
			->allowEmpty('username', false)
			->allowEmpty('displayname', false)
			->allowEmpty('password', false);

		$validator
			->add('username', [
				'unique' => [
				'message' => __('This name is already taken'),
				'provider' => 'table',
				'rule' => 'validateUnique'
				]
			]);

		$validator
			->add('password', 'notEmpty', [
				'rule' => ['lengthBetween', 60, 60],
				'message' => __('The hashed password must 60 chars')
			]);

		return $validator;
	}

}

