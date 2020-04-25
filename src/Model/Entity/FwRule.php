<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FwRule Entity
 *
 * @property string $default_ipfilter
 * @property int $id
 * @property int $rule_number
 * @property int $profile_id
 * @property string $dest_ip_type
 * @property string $dest_ip
 * @property int $dest_ip_mask
 * @property string $dest_iprange_first
 * @property string $dest_iprange_last
 * @property string $dest_hostname
 * @property string $protocol
 * @property string $dest_ports
 * @property string $target
 * @property int $enabled
 * @property string $dest_hostname_ip
 * @property int $timestamp
 * @property int $c_timestamp
 *
 * @property \App\Model\Entity\Profile $profile
 */
class FwRule extends Entity
{

}
