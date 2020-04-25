<?php
/**
 * @author Benoit Saglietto <bsaglietto[AT]keexybox.org>
 *
 * @copyright Copyright (c) 2020, Benoit SAGLIETTO
 * @license GPLv3
 *
 * This file is part of Keexybox project.

 * Keexybox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Keexybox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Keexybox. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace App\Controller\Component;

require_once(APP .DS. 'Controller' . DS . 'Component' . DS . 'IP4Calc.php');
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use keexybox\IP4Calc;

/**
 * This component gives informations of Keexybox network configuration
 * It also do Ipv4 conversions
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class Ipv4Component extends Component
{
    /**
     * This function returns associated network of given ip/netmask
     * Be careful, Ipv4 have to be validated before submitting param to this function
     * 
     * @return String Network for example 192.168.2.0
     */
    public function getNetwork($ip, $netmask)
    {
        // Retrieve network input IP host settings
        $oIP = new IP4Calc($ip, $netmask);
        $oNet = $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);

        return $oNet;
    }

    /**
     * This function return associated decimal mask of given ip/netmask
     * Be careful, Ipv4 have to be validated before submitting param to this function
     * 
     * @return String Mask in decimal. Example 24 for a 255.255.255.0 mask
     */
    public function getMaskDec($ip, $netmask)
    {
        // Retrieve network input IP host settings
        $oIP = new IP4Calc($ip, $netmask);
        $oNet = $oIP->get(IP4Calc::NETMASK, IP4Calc::DECIMAL);

        return $oNet;
    }


    /**
     * Get useful informations to know about Keexybox network input interface
     * 
     * @return Array 
     */
    public function getInputInfo()
    {
        $this->Config = TableRegistry::getTableLocator()->get('Config');
        $host_ip = $this->Config->get('host_ip_input')->value;
        $host_netmask = $this->Config->get('host_netmask_input')->value;
        $info = [
            'ip' => $host_ip,
            'netmask' => $host_netmask,
            'network' => $this->getNetwork($host_ip, $host_netmask),
            'mask_dec' => $this->getMaskDec($host_ip, $host_netmask),
            ];
        return $info;

    }

    /**
     * Get useful informations to know about Keexybox network output interface
     * 
     * @return Array 
     */
    public function getOutputInfo()
    {
        $this->Config = TableRegistry::getTableLocator()->get('Config');
        $host_ip = $this->Config->get('host_ip_output')->value;
        $host_netmask = $this->Config->get('host_netmask_output')->value;
        $host_gateway = $this->Config->get('host_gateway')->value;
        $info = [
            'ip' => $host_ip,
            'netmask' => $host_netmask,
            'gateway' => $host_gateway,
            'network' => $this->getNetwork($host_ip, $host_netmask),
            'mask_dec' => $this->getMaskDec($host_ip, $host_netmask),
            ];
        return $info;
    }
}

?>

