<?php
namespace App\Model\Table;

use App\Model\Entity\ProfilesRouting;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Rule\IsUnique;

/**
 * ProfilesRouting Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 */
class ProfilesRoutingTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('profiles_routing');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Profiles', [
            'foreignKey' => 'profile_id',
            'joinType' => 'INNER'
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
            ->requirePresence('address', 'create')
            ->notEmpty('address', __('The value should not be empty'));
            
        $validator
            ->requirePresence('routing', 'create')
            ->notEmpty('routing');

        return $validator;
    }

    public function validationIpaddr(Validator $validator)
    {
        $validator
		  ->add('address', 'notEmpty', [
			'rule' => ['ip', 'ipv4'],
			'message' => __('Invalid IP address')
		]);
        return $validator;
    }

    public function validationFqdn(Validator $validator)
    {
        $validator
		->add('address', 'notEmpty', [
			// Regex for FQDN
			'rule' => ['custom', "/(?=^.{4,253}$)(^((?!-)[a-zA-Z0-9-]{1,63}(?<!-)\.)+[a-zA-Z]{2,63}$)/i"],
			'message' => __('Invalid domain')
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
		$rules->add($rules->isUnique(['profile_id', 'address']));
        return $rules;
    }
}
