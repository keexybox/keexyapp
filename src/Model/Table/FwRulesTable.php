<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FwRules Model
 * This model points to a table view
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 */
class FwRulesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('fw_rules');

        $this->belongsTo('Profiles', [
            'foreignKey' => 'profile_id'
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
            ->allowEmpty('default_ipfilter');

        $validator
            ->integer('id')
            ->allowEmpty('id');

        $validator
            ->integer('rule_number')
            ->allowEmpty('rule_number');

        $validator
            ->allowEmpty('dest_ip_type');

        $validator
            ->allowEmpty('dest_ip');

        $validator
            ->integer('dest_ip_mask')
            ->allowEmpty('dest_ip_mask');

        $validator
            ->allowEmpty('dest_iprange_first');

        $validator
            ->allowEmpty('dest_iprange_last');

        $validator
            ->allowEmpty('dest_hostname');

        $validator
            ->allowEmpty('protocol');

        $validator
            ->allowEmpty('dest_ports');

        $validator
            ->allowEmpty('target');

        $validator
            ->integer('enabled')
            ->allowEmpty('enabled');

        $validator
            ->allowEmpty('dest_hostname_ip');

        $validator
            ->integer('timestamp')
            ->allowEmpty('timestamp');

        $validator
            ->integer('c_timestamp')
            ->allowEmpty('c_timestamp');

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
