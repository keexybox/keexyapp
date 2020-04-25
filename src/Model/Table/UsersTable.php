<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 * If this this model is modified, don't forget to duplicate the modifications into UsersNoHashPassword model.
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 *
 * @property \Cake\ORM\Association\hasMany $ConnectionsHistory
 */
class UsersTable extends Table
{
	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config)
	{
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
			->add('confirm_password', 'notEmpty', [
				'rule' => ['compareWith', 'password'],
				'message' => __('Passwords do not match')
			]);

		$validator
			->add('password', 'notEmpty', [
				'rule' => ['lengthBetween', 8, 20],
				'message' => __('Password must contain at least 8 characters')
			]);

		return $validator;
	}
}

