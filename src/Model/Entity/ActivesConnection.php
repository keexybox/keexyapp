<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TrackingSession Entity.
 */
class ActivesConnection extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
		'ip' => true,
        'name' => true,
        'name_id' => true,
        'user_id' => true,
        'device_id' => true,
        'profile_id' => true,
        'type' => true,
        'status' => true,
        'mac' => true,
        'start_time' => true,
        'end_time' => true,
        'profile' => true,
        'display_start_time' => true,
        'display_end_time' => true,
    ];
}
