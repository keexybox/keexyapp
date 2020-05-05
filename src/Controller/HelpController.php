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

namespace App\Controller;

use App\Controller\AppController;
use Cake\Error\Debugger;
use Cake\Datasource\ConnectionManager;

use Cake\Cache\Cache;

/**
 * This class showing licences and help information
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \App\Model\Table\ConfigTable $Config
 */
class HelpController extends AppController
{
    /**
     * This function shows Keexybox license information
     *
     * @return void
     */
    public function licenses()
    {

        $this->set('kxb_license', file_get_contents(ROOT."/src/COPYING"));

        $keexybox_root_dir = $this->Config->get('keexybox_root_dir')->value;
        $kxb_src_code_dirs = [
            ROOT."/src/",
            //ROOT."/vendor/keexybox/",
            //$keexybox_root_dir."/scripts/"
            ];

        $this->set('kxb_src_code_dirs', $kxb_src_code_dirs);

        $this->set('version', $this->Config->get('version')->value);

        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * This function shows Keexybox license information during wizard
     *
     * @return void
     */
    public function wlicenses()
    {
        $this->licenses();
        $this->viewBuilder()->setLayout('wizard');
    }
}
?>
