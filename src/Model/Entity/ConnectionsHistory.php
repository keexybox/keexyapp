<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ConnectionsHistory Entity
 *
 * @property int $id
 * @property string $ip
 * @property int $name_id
 * @property int $profile_id
 * @property string $type
 * @property string $mac
 * @property float $start_time
 * @property float $end_time
 * @property \Cake\I18n\Time $display_start_time
 * @property \Cake\I18n\Time $display_end_time
 *
 * @property \App\Model\Entity\Name $name
 * @property \App\Model\Entity\Profile $profile
 */
class ConnectionsHistory extends Entity
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
