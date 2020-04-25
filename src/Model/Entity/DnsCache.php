<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DnsCache Entity.
 */
class DnsCache extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'fqdn' => true,
        'ip' => true,
        'timestamp' => true,
        'c_timestamp' => true,
    ];
}
