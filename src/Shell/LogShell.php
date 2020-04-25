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
 * This class allow to manage DNS logs import and retention into the database
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class LogShell extends BoxShell
{
    /**
     * This function run the python script to import DNS logs into database
     *
     * @param $logfile: path of log file to import
     * @param $log_per_sql_query: number of log lines to import per SQL query
     * @param $from_line (Integer) : first line to import from.
     * @param $last_lines (Integer) : last line to import. Stop importing after this line.
     * 
     * @return void
     */
    private function ImportDnsLog($log_file, $log_per_sql_query = null, $from_line = null, $last_lines = null)
    {
        parent::initialize();
        $import_dnsqueries_script = $this->scripts_dir . "/import-dnsqueries.py";

        if($log_per_sql_query == null) { $log_per_sql_query = 10000;}

        $cmd_line = "$this->bin_python $import_dnsqueries_script -f $log_file -n $log_per_sql_query";

        if(isset($from_line) and is_numeric($from_line)) {
            $cmd_line .= " -l $from_line";
        }
        if(isset($last_lines) and is_numeric($last_lines)) {
            $cmd_line .= " -t $last_lines";
        }
        exec($cmd_line, $output, $rc);
    }


    /**
     * This function retrieve all log files to import, the submit them to ImportDnsLog()
     *
     * @return void
     */
    private function ImportAllDnsLog()
    {
        parent::initialize();
        $log_dir = $this->keexyboxlogs . "/";
        $log_files = glob("$log_dir/bind_queries.log*");

        foreach($log_files as $log_file) {
            $this->out("importing $log_file...");
            $this->ImportDnsLog($log_file);
        }
    }

    /**
     * This function delete all DnsLog from database older than given $date
     *
     * @param $date : date_time format ex : "2017-12-08 23:59:59"
     * 
     * @return : true/false
     */
    private function PurgeDnsLogBefore($date)
    {
        parent::initialize();
        $this->loadModel('DnsLog');
        return $this->DnsLog->deleteAll(['date_time <' => $date]);

    }

    /**
     * This function update rotated logs that may not have been imported from current log file
     *
     * @return void
     */
    public function UpdateRotatedDnsLog()
    {
        parent::initialize();
        $this->loadModel('DnsLog');

        // Check last imported log file
        // SELECT DISTINCT `logfile_name` FROM `access_log` WHERE `logfile_name` LIKE 'access.log-________%' ORDER BY `logfile_name` DESC LIMIT 1
        $last_log_file = $this->DnsLog->find('all', ['conditions' => ['logfile_name LIKE' => 'bind_queries.log-________%']])
                ->hydrate(false)
                ->select(['logfile_name'])
                ->distinct(['logfile_name'])
                ->order(['logfile_name' => 'DESC'])
                ->limit(1)
                ->toArray();

        if(!empty($last_log_file)) {
            // Check last imported line in last log file
            // SELECT `line_number` FROM `access_log` WHERE `logfile_name` = 'access.log-20171115' ORDER BY `line_number` DESC LIMIT 1 
            $last_log_line = $this->DnsLog->find('all', ['conditions' => ['logfile_name' => $last_log_file[0]['logfile_name']]])
                ->hydrate(false)
                ->select(['line_number'])
                ->order(['line_number' => 'DESC'])
                ->limit(1)
                ->toArray();
    
    
            preg_match_all('!\d+!', $last_log_file[0]['logfile_name'], $matches);
    
            $first_date_to_import = $matches[0][0];
            $last_date_to_import = date("Ymd");
    
            $import_date = $first_date_to_import;
    
            $log_dir = $this->keexyboxlogs . "/";
    
            $from_line = $last_log_line[0]['line_number'];
    
            $this->out('importing '.$log_dir.'/bind_queries.log-'.$import_date.'...');
            $this->ImportDnsLog($log_dir."/bind_queries.log-".$import_date, 10000, $from_line);
            $import_date++;
    
            while($import_date <= $last_date_to_import) {
                $this->out('importing '.$log_dir.'/bind_queries.log-'.$import_date.'...');
                $this->ImportDnsLog($log_dir."/bind_queries.log-".$import_date, 10000);
                $import_date++;
            }
        } else {
            $this->ImportAllDnsLog();
        }
    }

    /**
     * This function purge logs from database older than retention defined by keexybox
     *
     * @return void
     */
    public function PurgeLog()
    {
        parent::initialize();
        $retention_date = date('Y-m-d H:i:s', time() - $this->log_db_retention*24*3600);
        $this->PurgeDnsLogBefore($retention_date);
    }

    /**
     * This function import the current DNS log file into database.
     *
     * @return void
     */
    public function ImportLastDnsLog()
    {
        parent::initialize();
        $log_file = $this->keexyboxlogs . "/bind_queries.log";
        $this->ImportDnsLog($log_file, 5000, null, 5000);
    }
}
?>
