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

Namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;

/**
 * This class manage Domains Routing Cache
 * DomainsRoutingCache consists of storing the set of IP addresses associated with domains name in the database and keeping it up-to-date.
 * 
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class DomainsRoutingCacheShell extends BoxShell
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('DnsCache');
    }

    public function main()
    {
        $this->out('Script to manage resolve host and cache DNS');
    }

    /**
     * This function list all resolved IP of a FQDN
     *
     * @param fqdn : Fully Qualified Domain Name of Website
     *
     * @return array of IPs found in database for given FQDN
     */
    public function ListIpsOfHost($fqdn)
    {
        $ips = $this->DnsCache->findByFqdn($fqdn);

        if(isset($ips)) {
            foreach($ips as $ip) {
                $ipslist[] = $ip['ip'];
            }

            if(isset($ipslist)) { 
                return $ipslist; 
            }
        }
    }

    /**
     * This function update the DNS Cache table
     *
     * @param fqdn : Fully Qualified Domain Name of Website
     * @param ip : ip resolved for FQDN
     *
     * @return 'new' if it is a new IP for this FQDN and 'uptate' if IP exist and been updated
     */
    public function UpdateDnsCache($fqdn, $ip)
    {
        parent::initialize();
        $this->loadModel('DnsCache');
        $cache = $this->DnsCache->find('all', [ 
                    'conditions' => [
                    'DnsCache.ip' => $ip,
                    'DnsCache.fqdn' => $fqdn,
                    ]])->first();

        if(isset($cache->id)) {
            /* 
             * This update DNS cache for hosts revolved again by DNS request. 
              * It update the timestamp to avoid de deletion by DeleteExpiredDnsCache
              */
            // Update only if it expire next day
            $expir_delay = $cache->timestamp + $this->dns_expiration_delay - time();
            if($expir_delay < 86400) { 
                $data = array(
                    'fqdn' => $cache->fqdn,
                    'ip' => $cache->ip,
                    'timestamp' => time()
                    );
                $resolvedhost = $this->DnsCache->patchEntity($cache, $data);
                $this->DnsCache->save($cache);
                $this->LogMessage("Update $cache->fqdn -> $cache->ip", 'dnscache');
                return('update');
            }
        } else {
            /* 
             * This add a new DNS cache for hosts revolved by DNS request.
             */
            $data = array(
                'fqdn' => $fqdn,
                'ip' => $ip,
                'timestamp' => time(),
                'c_timestamp' => time()
                );

            $cache = $this->DnsCache->newEntity();
            $cache = $this->DnsCache->patchEntity($cache, $data);
            $this->DnsCache->save($cache);
            $this->LogMessage("Add $cache->fqdn -> $cache->ip", 'dnscache');
            return('new');
        }
    }

    /**
     * This function remove all expired ip that were not updated for a long time (after the $this->dns_expiration_delay)
     *
     * @return array of all removed IPs and the fqdn
     */
    public function DeleteExpiredDnsCache()
    {
        parent::initialize();
        $expiration = time() - $this->dns_expiration_delay;
        //$expiration = 1490498381;
        $expired_fqdns = $this->DnsCache->find('all', ['conditions' => [ 
                'DnsCache.timestamp <' => $expiration]
                //'DnsCache.timestamp >' => $expiration] // for testing
                ]);

        $this->DnsCache->deleteAll(['DnsCache.timestamp <' => $expiration]);

        $removed_hosts = null;

        foreach($expired_fqdns as $expired_fqdn) {
            //$this->out("Remove $expired_fqdn->fqdn -> $expired_fqdn->ip");
            $removed_hosts[] = ['fqdn' => $expired_fqdn->fqdn, 'ip' => $expired_fqdn->ip];
            $this->LogMessage("Remove $expired_fqdn->fqdn -> $expired_fqdn->ip", 'dnscache');
        }
        //print_r($removed_hosts);
        return $removed_hosts;
    }

    /**
     * This function resolve domain and should be use when user of device connect to network
     *
     * @param fqdn : Fully Qualified Domain Name of Website
     *
     * @return array of all resolved IPs found for given fqdn
     */
    public function ResolveHost($fqdn)
    {
        parent::initialize();

        // DNS request on FQDN
        $ips = gethostbynamel($fqdn);

        // Define var for list of IP 
        $iplist = null;

        if(!empty($ips)) {
            foreach ($ips as $ip)
            {
                $this->UpdateDnsCache($fqdn, $ip);
                $iplist = $this->ListIpsOfHost($fqdn);
            }
        }
        return $iplist;
    }

    /**
     * This function resolve domain and should be use by keexybox daemon
     *
     * @param fqdn : Fully Qualified Domain Name of Website
     *
     * @return array of updated IPs and new IPs found for given fqdn
     */
    public function ResolveHostDaemon($fqdn)
    {
        parent::initialize();

        $ips = gethostbynamel($fqdn);

        $update = null;
        $new = null;
        foreach ($ips as $ip) {
            //On stock les nouvelles ips dans une variable pour les retourner 
            $res = $this->UpdateDnsCache($fqdn, $ip);
            if($res == 'update') {
                $update[] = $ip;
            }
            elseif($res == 'new') {
                $new[] = $ip;
            }
        }
        $ips = ['update' => $update, 'new' => $new];
        return $ips;
    }
}
