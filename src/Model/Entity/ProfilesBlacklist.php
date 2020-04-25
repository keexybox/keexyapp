<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProfilesBlacklist Entity
 *
 * @property int $id
 * @property int $profile_id
 * @property string $category
 *
 * @property \App\Model\Entity\Profile $profile
 */
class ProfilesBlacklist extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
