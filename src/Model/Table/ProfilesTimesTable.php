<?php
namespace App\Model\Table;

use App\Model\Entity\ProfilesTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProfilesTimes Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 */
class ProfilesTimesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('profiles_times');
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
            ->requirePresence('daysofweek', 'create')
            ->notEmpty('daysofweek');
            
        $validator
            ->requirePresence('timerange', 'create')
            ->notEmpty('timerange');

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
		// daysofweek, timerange, profile_id must be unique together
		$rules->add($rules->isUnique(['daysofweek', 'timerange', 'profile_id']));
        return $rules;
    }
}
