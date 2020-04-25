<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Route Entity
 *
 * @property int $profile_id
 * @property string $default_routing
 * @property string $fqdn
 * @property string $dstip
 * @property int $timestamp
 * @property int $c_timestamp
 * @property string $forwarder
 * @property int $https_tracking
 *
 * @property \App\Model\Entity\Profile $profile
 */
class Route extends Entity
{

}
