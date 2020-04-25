<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Config Entity.
 */
class Config extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'value' => true,
    ];
}
