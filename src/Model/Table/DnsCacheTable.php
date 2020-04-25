<?php
namespace App\Model\Table;

use App\Model\Entity\DnsCache;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DnsCache Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 */
class DnsCacheTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('dns_cache');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
            ->requirePresence('fqdn', 'create')
            ->notEmpty('fqdn');
            
        $validator
            ->requirePresence('ip', 'create')
            ->notEmpty('ip');
            
        $validator
            ->add('timestamp', 'valid', ['rule' => 'numeric'])
            ->requirePresence('timestamp', 'create')
            ->notEmpty('timestamp');

        $validator
            ->add('c_timestamp', 'valid', ['rule' => 'numeric'])
            ->requirePresence('c_timestamp', 'create')
            ->notEmpty('c_timestamp');

        return $validator;
    }
}
