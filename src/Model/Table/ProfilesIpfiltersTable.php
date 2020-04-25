<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\Rule\IsUnique;

/**
 * ProfilesIpfilters Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \Cake\ORM\Association\BelongsTo $Profiles
 *
 */
class ProfilesIpfiltersTable extends Table
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

        $this->setTable('profiles_ipfilters');
        $this->setDisplayField('rule_number');
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('dest_ip_type', 'create')
            ->notEmpty('dest_ip_type');


        $validator
            ->requirePresence('protocol', 'create')
            ->notEmpty('protocol');
		
        $validator
            ->requirePresence('dest_ports', 'create')
			->allowEmpty('dest_ports');
			

        $validator
            ->requirePresence('target', 'create')
            ->notEmpty('target');

        $validator
            ->boolean('enabled')
            ->requirePresence('enabled', 'create')
            ->notEmpty('enabled');

        return $validator;
    }

    public function validationNet(Validator $validator)
	{
		$validator = $this->validationDefault($validator);

		$validator
			->notEmpty('dest_ip', __('The IP address is required'))
			->notEmpty('dest_ip_mask', __('The netmask is required'))
			->allowEmpty('dest_iprange_first')
			->allowEmpty('dest_iprange_last')
			->allowEmpty('dest_hostname');

        $validator
			->add('dest_ip', 'notEmpty', [
				'rule' => ['ip', 'ipv4'],
				'message' => __('Invalid IP address')
			]);

        return $validator;

	}

    /**
     * Iprange validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationIprange(Validator $validator)
    {
		$validator = $this->validationDefault($validator);

		$validator
			->notEmpty('dest_iprange_first', __('The IP address is required'))
			->notEmpty('dest_iprange_last', __('The IP address is required'))
			->allowEmpty('dest_ip')
			->allowEmpty('dest_ip_mask')
			->allowEmpty('dest_hostname');

        $validator
			->add('dest_iprange_first', 'ipv4', [
				'rule' => ['ip', 'ipv4'],
				'message' => __('Invalid IP address')
			]);

        $validator
			->add('dest_iprange_last', 'ipv4', [
				'rule' => ['ip', 'ipv4'],
				'message' => __('Invalid IP address')
			]);

        return $validator;
    }

    /**
     * Iprange validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationFqdn(Validator $validator)
    {
		$validator = $this->validationDefault($validator);

		$validator
			->notEmpty('dest_hostname')
			->allowEmpty('dest_iprange_first')
			->allowEmpty('dest_iprange_last')
			->allowEmpty('dest_ip')
			->allowEmpty('dest_ip_mask');

        $validator
		->add('dest_hostname', 'notEmpty', [
			// Regex for FQDN
			'rule' => ['custom', "/(?=^.{4,253}$)(^((?!-)[a-zA-Z0-9-]{1,63}(?<!-)\.)+[a-zA-Z]{2,63}$)/i"],
			'message' => __('Invalid Hostname')
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
		$rules->add($rules->isUnique(['profile_id', 'dest_ip', 'protocol', 'dest_ports']));
		$rules->add($rules->isUnique(['profile_id', 'dest_iprange_first', 'dest_iprange_last', 'protocol', 'dest_ports']));
		$rules->add($rules->isUnique(['profile_id', 'dest_hostname', 'protocol', 'dest_ports']));

        return $rules;
    }
}
