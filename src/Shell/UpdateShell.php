<?php
/**
 * @author Benoit Saglietto <bsaglietto[AT]keexybox.org>
 *
 * @copyright Copyright (c) 2021, Benoit SAGLIETTO
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
 * This class is use to manage KeexyBox updates
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class UpdateShell extends BoxShell
{

    public function main(){}
    public function startup(){}

    public function download($download_url)
    {
        parent::initialize();
        $expl_url = explode("/", $download_url);
        $update_file = $this->tmp_dir."/".end($expl_url);
        file_put_contents($update_file, fopen($download_url, 'r'));
    }

    public function extractPkg($update_file = null)
    {
        parent::initialize();
        $update_file = "/opt/keexybox/tmp/keexybox_20.10.3_raspbian10.tar.gz";
        $phar = new \PharData($update_file);
        $phar->extractTo($this->tmp_dir);

    }

    public function run($download_url)
    {
        parent::initialize();
        $this->download($download_url);
    }
}
