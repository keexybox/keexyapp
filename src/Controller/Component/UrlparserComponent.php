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

use Cake\Controller\Component;

/**
 * This component contains useful method related to Date and Time
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class UrlparserComponent extends Component
{
    /*
     * This function split part of an URL into a array
     * 
     * @param String $url: example "http://www.domain.com/path"
     *
     * @return Array:
     *    [
     *        'fqdn' => 'www.domain.com',
     *        'host' => 'www.domain.com',
     *        'url' => 'www.domain.com/path',
     *        'path' => '/path',
     *        'type' => 'u'
     *    ]
     */
    public function Parseurl($url)
    {
        $urlparse = parse_url($url);
        //print_r($urlparse);
        if(isset($urlparse['scheme']) and isset($urlparse['host'])) {
            $website['fqdn'] = $urlparse['host'];
            $website['host'] = $urlparse['host'];
        }
        elseif(!isset($urlparse['scheme']) and !isset($urlparse['host'])) {
            // Adding http:// to URL if were not specified to summit again to parse_url()
            $url = "http://".$url;
            $urlparse = parse_url($url);
            $website['fqdn'] = $urlparse['host'];
            $website['host'] = $urlparse['host'];
            }
        elseif(isset($urlparse['scheme']) and !isset($urlparse['host'])) {
            $website['fqdn'] = null;
            }
    
        $website['url'] = null;
        if(isset($urlparse['host'])) {
            $website['url'] = $urlparse['host'];
                if(isset($urlparse['path']) and $urlparse['path'] != '/' ) {
            $website['url'] .= $urlparse['path'];
            $website['path'] = $urlparse['path'];
                }
        }
    
        if(isset($website['fqdn']) and $website['fqdn'] == $website['url']) {
            $website['type'] = 'd';
        } else {
            $website['type'] = 'u';
        }
    
        return $website;
    }
}
?>
