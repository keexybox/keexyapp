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
 * This class start or stop Keexybox
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class InitShell extends BoxShell
{
    /**
     * This function starts all services when Keexybox start
     *
     * @return void
     */
    public function start() {
        parent::initialize();

        // Update de scripts config on startup
        $config = new ConfigShell;
        $config->scripts('all', false);

        $service = new ServiceShell;
        // Start all services needed by Keexybox
        $service->dhcp('start', 'noexit');
        $service->hostapd('start', 'noexit');
        $service->bind('start', 'noexit');
        $service->tor('start', 'noexit');
        $service->rules('start', 'noexit');
    }

    /**
     * This function stops all services when Keexybox stop
     *
     * @return void
     */
    public function stop() {
        parent::initialize();
        $service = new ServiceShell;

        // Stop all services needed by Keexybox
        $service->rules('stop', 'noexit');
        $service->tor('stop', 'noexit');
        $service->bind('stop', 'noexit');
        $service->hostapd('stop', 'noexit');
        $service->dhcp('stop', 'noexit');
    }
}
