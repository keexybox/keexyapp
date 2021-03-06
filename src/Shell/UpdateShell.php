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

require_once(APP .DS. 'Controller' . DS . 'Component' . DS . 'ShellComponent.php');
use Cake\Console\Shell;
use Cake\Core\Configure;
use keexybox\ShellComponent;

/**
 * This class is use to manage KeexyBox updates
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class UpdateShell extends BoxShell
{

    public function main(){}
    public function startup(){}

    /**
     * This function download the archive that contain the KeexyBox update
     *
     * @param $download_url: example: https://download.keexybox.org/keexybox_20.10.2_raspbian10.tar.gz
     *
     * @return string: local path of the archive file
     */
    public function download($download_url)
    {
        parent::initialize();
        $expl_url = explode("/", $download_url);
        $update_file = $this->tmp_dir."/".end($expl_url);
        file_put_contents($update_file, fopen($download_url, 'r'));
        return $update_file;
    }

    /**
     * This function extract archive that contains the update
     *
     * @param $update_file: path to the archive to extract
     *
     * @return string: return the subfolder name of the archive
     */
    public function extractPkg($update_file = null)
    {
        parent::initialize();
        $shell_component = new ShellComponent('ShellComponent');

        $install_dir = null;
        //$update_file = "/opt/keexybox/tmp/keexybox_20.10.2_raspbian10.tar.gz";
        $phar = new \PharData($update_file);
        foreach($phar as $file) { 
            if($file->isDir()) {
                $expl_file = explode("/", $file);
                // Define target path to extract files
                $install_dir = $this->tmp_dir."/".end($expl_file);
                // Extract files to keexybox tmp dir
                $phar->extractTo($this->tmp_dir, null, true);
                return $install_dir;
            }
        }
    }

    /**
     * This function update keexybox
     *
     * @param $update_script: path to the script do run
     *
     * @return array: [ 0 => "return code", 1 => 'path to log file of installation' ]
     */
    public function install($update_script = null) {
        $log_time_stamp = date('YmdHis');
        $install_log_file_name = $this->keexyboxlogs."/keexybox_update_".$log_time_stamp.".log";
        //passthru($this->tmp_dir."/test.sh"." > ".$install_log_file_name."2>&1", $update_res);
        passthru($update_script." > ".$install_log_file_name." 2>&1", $update_res);
        $res_info = [ $update_res, $install_log_file_name ];
        //$res_info = serialize($res_info);
        return $res_info;
    }

    /**
     * This function run above functions
     *
     * @param $update_script: path to the script do run
     *
     * @return array: [ 0 => "return code", 1 => 'path to install log file' ]
     * @output: path to log file of installation
     */
    public function run($download_url)
    {
        parent::initialize();
        $update_file = $this->download($download_url);
        $install_dir = $this->extractPkg($update_file);
        $update_res = $this->install($install_dir."/install.sh");
        //$update_res = $this->install("/opt/keexybox/tmp/test.sh");
        $this->out($update_res[1]);
        return $update_res[0];
    }
}
