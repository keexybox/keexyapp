<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProfilesRouting Entity.
 */
class ProfilesRouting extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'address' => true,
        'routing' => true,
        'category' => true,
        'profile_id' => true,
        'enabled' => true,
        //'profile' => true,
    ];
}
