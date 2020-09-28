<?php
namespace App\Model\Table;

use App\Model\Entity\Config;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Config Model
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 */
class ConfigTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->setTable('config');
        $this->setDisplayField('param');
        $this->setPrimaryKey('param');
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
            ->allowEmpty('param', 'create');
            
        $validator
            ->requirePresence('value', 'create')
            //->notEmpty('value');
            ->notEmpty('value');

        return $validator;
    }

    /**
     * Ipv4 validation rule.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationIpaddr(Validator $validator)
    {
        $validator
		  ->add('value', 'notEmpty', [
			'rule' => ['ip', 'ipv4'],
			'message' => __('Invalid IP address')
		]);
        return $validator;
    }

    /**
     * Ipv4 validation rule and allow empty value.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationEmptyipaddr(Validator $validator)
    {
        $validator
		  ->add('value', 'Empty', [
			'rule' => ['ip', 'ipv4'],
			'message' => __('Invalid IP address')
		]);

        $validator
            ->allowEmpty('value', 'update');

        return $validator;
    }

    /**
     * Alphanumeric validation rule.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationAlphanum(Validator $validator)
    {
        $validator
		->add('value', 'notEmpty', [
			'rule' => ['alphaNumeric'],
			'message' => __('The value must be alphanumeric')
		]);
        return $validator;
    }

    /**
     * Custom name validation rule.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationNames(Validator $validator)
    {
        $validator
		->add('value', 'notEmpty', [
			'rule' => ['custom', "/^(?![\s'-])(?:[\s'-]{0,1}[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}])+$/Du"],
			'message' => __('Invalid name')
		]);
        return $validator;
    }

    /**
     * Custom Wifi Access Point SSID validation rule.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationSsid(Validator $validator)
    {
        $validator
			->add('value', 'hostapd', [
				'rule' => ['custom', "/(?=^[a-zA-Z0-9_\-]*$)/i"],
				'message' => __('The value must be alphanumeric and may contain hyphens (-) and underscores (_)')
        ]);
        return $validator;
    }

    /**
     * Alpha only validation rule.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationAlpha(Validator $validator)
    {
        $validator
		->add('value', 'notEmpty', [
			'rule' => ['custom', "/^[a-zA-Z]+$/"],
			'message' => __('Invalid name')
		]);
        return $validator;
    }

    /**
     * Fully qualified domain name (FQDN) validation rule.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationFqdn(Validator $validator)
    {
        $validator
		->add('value', 'notEmpty', [
			// Regex for FQDN
			//'rule' => ['custom', "/(?=^.{1,254}$)(^(?:(?!\d|-)[a-z0-9\-]{1,63}(?<!-)\.)+(?:[a-z]{2,})$)/i"],
			'rule' => ['custom', "/(?=^.{4,253}$)(^((?!-)[a-zA-Z0-9-]{1,63}(?<!-)\.)+[a-zA-Z]{2,63}$)/i"],
			// Regex for FQDN or only one *(star) allow
			//'rule' => ['custom', "/((?=^.{1,254}$)(^(?:(?!\d|-)[a-z0-9\-]{1,63}(?<!-)\.)+(?:[a-z]{2,})$)|^\*+$)/i"],
			'message' => __('Invalid domain')
			]);
        return $validator;
    }

    /**
     * Range validation rule for DNS routing cache expiration.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
	public function validationDns_expiration_delay(Validator $validator)
	{
		$validator
			->add('value', 'valid_range', [
					'rule' => ['range', 86400, 1296000],
					'message' => __('The value must be between {0} and {1} days', '1', '15'),
				]);
					
        return $validator;
	}

    /**
     * Range validation rule for user connection duration.
	 * ***** CHECK IF THIS VALIDATION STILL TO BE USE *****
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
     /*
	public function validationConnection_time(Validator $validator)
	{
		$validator
			->add('value', 'valid_range', [
					'rule' => ['range', 1, 112800],
					'message' => __('Expiration must be defined between {0} and {1} hours (6.5 days)', '1', '8760'),
				]);
        return $validator;
	}
    */

    /**
     * Range validation rule for log retention.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
	public function validationLogs_retention(Validator $validator)
	{
		$validator
			->add('value', 'valid_range', [
					'rule' => ['range', 1, 36500],
					'message' => __('The value must be between {0} and {1} days', '1', '36500'),
				]);
        return $validator;
	}

    /**
     * Registration Code validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationRegcode(Validator $validator)
    {
        // Allow blank registration Code
        $validator
            ->requirePresence('value', 'create')
            ->allowEmpty('value', 'update');
            
        return $validator;
    }

    /**
     * Tor Exit Nodes Country validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationTor_exitnodes_countries(Validator $validator)
    {
        // Allow blank registration Code
        $validator
            ->requirePresence('value', 'create')
            ->allowEmpty('value', 'update');
            
        return $validator;
    }

}
