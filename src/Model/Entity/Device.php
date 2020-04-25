<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Device Entity.
 */
class Device extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'devicename' => true,
        'mac' => true,
        'lang' => true,
        'enabled' => true,
        'dhcp_reservation_ip' => true,
        'profile_id' => true,
        'profile' => true,
    ];
}
