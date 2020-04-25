<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProfilesTime Entity.
 */
class ProfilesTime extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'daysofweek' => true,
        'timerange' => true,
        'profile_id' => true,
        'profile' => true,
    ];
}
