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
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\Network\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Time;

/**
 * This class allows to consult and manage the logs 
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class StatisticsController extends AppController
{
    /**
     * This function displays all requested domains 
     * This function can intercept GET query to filters data to display
     *
     * @return void
     */
    public function logs()
    {
        // Redirect query that depent on other view
        if(isset($this->request->query['view_type'])) {
            $view_type = $this->request->query['view_type'];
            if($view_type != 'logs') {
                return $this->redirect(['action' => $view_type, '?' => $this->request->query]);
            }
        }

        $this->loadModel('Config');
        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];
        date_default_timezone_set($timezone);

        // Set list of active users and devices
        $this->loadModel('ActivesConnections');
        $active_client_options = null;
        $active_connections = $this->ActivesConnections->find('all');
        foreach($active_connections as $active_connection) {
            $active_client_options[$active_connection->ip] = $active_connection->name." (".$active_connection->ip.")";
        }
        $this->set('active_client_options', $active_client_options);

        // Set language for datetime picker
        $lang = $this->request->session()->read('Config.language');
        $datetime_picker_locale = explode('_', $lang);
        $datetime_picker_locale = $datetime_picker_locale[0];
        $this->set('datetime_picker_locale', $datetime_picker_locale);

        // We get the system timezone offset
        $this->loadComponent('Times');
        $sys_tz_name = $this->Times->getSystemTimezone();
        $sys_tz_offset = $this->Times->getSystemTimezoneOffset();

        $this->loadModel('Profiles');
        $this->loadModel('DnsLog');
        $profiles = $this->Profiles->find('list');

        // By default the view show last 24 hours logs t avoid loading all logs
        // 24 hours time ago
        $time_24h_ago = new Time('24 hours ago');
        $time_24h_ago->timezone($sys_tz_name);
        $begin_date = $time_24h_ago->i18nFormat('yyyy-MM-dd HH:mm:ss');

        $dnslogs = $this->DnsLog->find();
        $dnslogs->select([
                'id',
                // Due to FrozenTime that work only with UTC, we convert date of log that were system time into UTC
                'date_time' => $dnslogs->func()->convert_tz(['date_time' => 'identifier',"$sys_tz_offset",'+00:00']),
                'client_ip',
                'profile_id',
                'keexybox_profiles.profilename',
                'domain',
                'blocked',
                'category',
                ])
            ->join(['table' => 'keexybox_profiles', 'type' => 'LEFT', 'conditions' => 'keexybox_profiles.id = profile_id'])
              ->where(['date_time >' => $begin_date]);

        // Set value of search Query to null by default
        $this->set('search_client_ip', null);
        $this->set('search_domain', null);
        $this->set('begin_date', $begin_date);
        $this->set('end_date', null);
        $this->set('view_type', null);
        $this->set('filter_status', null);
        $this->set('results', 25);

        if(isset($this->request->query['action']) and $this->request->query['action'] == 'search') {
            //$query = $this->request->query['domain'];


            $q = null;
            if($this->request->query['begin_date'] != '') {
                // displayed requested time by user in defined app time zone
                $begin_date = $this->request->query['begin_date'];
                
                // Query database with date refers to system timezone
                $q_begin_date = new Time($this->request->query['begin_date']);
                $q_begin_date->timezone($sys_tz_name);
                $q_begin_date = $q_begin_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $q[] = ['date_time >' => "$q_begin_date"];
            }
            if($this->request->query['end_date'] != '') {
                // displayed requested time by user in defined app time zone
                $end_date = $this->request->query['end_date'];

                // Query database with date refers to system timezone
                $q_end_date = new Time($this->request->query['end_date']);
                $q_end_date->timezone($sys_tz_name);
                $q_end_date = $q_end_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $q[] = ['date_time <' => "$q_end_date"];
            }
            if($this->request->query['client_ip'] != '') {
                $client_ip = $this->request->query['client_ip'];
                $q[] = ['client_ip' => "$client_ip"];
            }
            if($this->request->query['domain'] != '') {
                $domain = $this->request->query['domain'];
                $q[] = ['domain LIKE' => "%$domain%"];
            }
            if($this->request->query['filter_status'] != '') {
                $status = $this->request->query['filter_status'];
                $q[] = ['blocked' => "$status"];
            }


            $dnslogs = $this->DnsLog->find();
            $dnslogs->select([
                    'id',
                    'date_time' => $dnslogs->func()->convert_tz(['date_time' => 'identifier',"$sys_tz_offset",'+00:00']),
                    'client_ip',
                    'profile_id',
                    'keexybox_profiles.profilename',
                    'domain',
                    'blocked',
                    'category',
                    ])
                    ->join(['table' => 'keexybox_profiles', 'type' => 'LEFT', 'conditions' => 'keexybox_profiles.id = profile_id'])
                      ->where([$q]);


            $this->paginate = [
                'limit' => $this->request->query['results'],
                'order' => ['date_time' => 'desc']
            ];
            $this->set('search_client_ip', $this->request->query['client_ip']);
            $this->set('search_domain', $this->request->query['domain']);
            $this->set('begin_date', $this->request->query['begin_date']);
            $this->set('end_date', $this->request->query['end_date']);
            $this->set('view_type', $this->request->query['view_type']);
            $this->set('filter_status', $this->request->query['filter_status']);
            $this->set('results', $this->request->query['results']);
        } else {
            $this->paginate = [
                'limit' => 25,
                'order' => ['date_time' => 'desc']
            ];
        }

        
        $this->set('timezone', $timezone);
        $this->set('dnslogs', $this->paginate($dnslogs));
        $this->set('profiles', $profiles);
        $this->viewBuilder()->setLayout('adminlte');

    }

    /**
     * This function displays the most active clients (users and devices)
     * This function can intercept GET query to filters data to display
     *
     * @return void
     */
    public function mostActiveClients()
    {
        // Redirect query that depent on other view
        if(isset($this->request->query['view_type'])) {
            $view_type = $this->request->query['view_type'];
            if($view_type != 'most_active_clients') {
                return $this->redirect(['action' => $view_type, '?' => $this->request->query]);
            }
        }

        // Set list of active users and devices
        $this->loadModel('ActivesConnections');
        $active_client_options = null;
        $active_connections = $this->ActivesConnections->find('all');
        foreach($active_connections as $active_connection) {
            $active_client_options[$active_connection->ip] = $active_connection->name." (".$active_connection->ip.")";
        }
        $this->set('active_client_options', $active_client_options);

        // Set language for datetime picker
        $datetime_picker_locale = explode('_', $this->request->session()->read('Config.language'));

        $datetime_picker_locale = $datetime_picker_locale[0];
        $this->set('datetime_picker_locale', $datetime_picker_locale);

        // We get the system timezone offset
        $this->loadComponent('Times');
        $sys_tz_name = $this->Times->getSystemTimezone();
        $sys_tz_offset = $this->Times->getSystemTimezoneOffset();

        $this->loadModel('DnsLog');
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');

        //exec($this->kxycmd("log ImportLastDnsLog 5000 1000"), $output, $rc);
        $dnslogs = $this->DnsLog->find();

        $dnslogs->select([
                'client_ip',
                'queries_count' => $dnslogs->func()->count('*')
                ])
            ->group('client_ip')
            ->order(['queries_count' => 'DESC']);

        // Set value of search Query to null by default
        $this->set('search_client_ip', null);
        $this->set('search_domain', null);
        $this->set('begin_date', null);
        $this->set('end_date', null);
        $this->set('view_type', null);
        $this->set('filter_status', null);
        $this->set('results', 25);

        if(isset($this->request->query['action']) and $this->request->query['action'] == 'search') {
            $q = null;
            if($this->request->query['begin_date'] != '') {
                // displayed requested time by user in defined app time zone
                $begin_date = $this->request->query['begin_date'];
                
                // Query database with date refers to system timezone 
                $q_begin_date = new Time($this->request->query['begin_date']);
                $q_begin_date->timezone($sys_tz_name);
                $q_begin_date = $q_begin_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $q[] = ['date_time >' => "$q_begin_date"];
            }
            if($this->request->query['end_date'] != '') {
                // displayed requested time by user in defined app time zone
                $end_date = $this->request->query['end_date'];

                // Query database with date refers to system timezone 
                $q_end_date = new Time($this->request->query['end_date']);
                $q_end_date->timezone($sys_tz_name);
                $q_end_date = $q_end_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $q[] = ['date_time <' => "$q_end_date"];
            }
            if($this->request->query['client_ip'] != '') {
                $client_ip = $this->request->query['client_ip'];
                $q[] = ['client_ip' => "$client_ip"];
            }
            if($this->request->query['domain'] != '') {
                $domain = $this->request->query['domain'];
                $q[] = ['domain LIKE' => "%$domain%"];
            }
            if($this->request->query['filter_status'] != '') {
                $status = $this->request->query['filter_status'];
                $q[] = ['blocked' => "$status"];
            }

            $dnslogs = $this->DnsLog->find();

            $dnslogs->select([
                    'client_ip',
                    'queries_count' => $dnslogs->func()->count('*')
                    ])
                  ->where([$q])
                ->group('client_ip')
                ->order(['queries_count' => 'DESC']);

            $this->paginate = [
                'limit' => $this->request->query['results'],
                'order' => ['queries_count' => 'desc']
            ];
            $this->set('search_client_ip', $this->request->query['client_ip']);
            $this->set('search_domain', $this->request->query['domain']);
            $this->set('begin_date', $this->request->query['begin_date']);
            $this->set('end_date', $this->request->query['end_date']);
            $this->set('view_type', $this->request->query['view_type']);
            $this->set('filter_status', $this->request->query['filter_status']);
            $this->set('results', $this->request->query['results']);
        } else {
            $this->paginate = [
                'limit' => 25,
                'order' => ['queries_count' => 'desc']
            ];
        }
        $this->loadModel('Config');
        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];
        $this->set('timezone', $timezone);

        $this->set('dnslogs', $this->paginate($dnslogs));
        $this->set('profiles', $profiles);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * This function displays the most requested domains
     * This function can intercept GET query to filters data to display
     *
     * @return void
     */
    public function mostQueried()
    {
        // Redirect query that depent on other view
        if(isset($this->request->query['view_type'])) {
            $view_type = $this->request->query['view_type'];
            if($view_type != 'most_queried') {
                return $this->redirect(['action' => $view_type, '?' => $this->request->query]);
            }
        }

        // Set list of active users and devices
        $this->loadModel('ActivesConnections');
        $active_client_options = null;
        $active_connections = $this->ActivesConnections->find('all');
        foreach($active_connections as $active_connection) {
            $active_client_options[$active_connection->ip] = $active_connection->name." (".$active_connection->ip.")";
        }
        $this->set('active_client_options', $active_client_options);

        // Set language for datetime picker
        $datetime_picker_locale = explode('_', $this->request->session()->read('Config.language'));
        $datetime_picker_locale = $datetime_picker_locale[0];
        $this->set('datetime_picker_locale', $datetime_picker_locale);

        // We get the system timezone offset
        $this->loadComponent('Times');
        $sys_tz_name = $this->Times->getSystemTimezone();
        $sys_tz_offset = $this->Times->getSystemTimezoneOffset();

        $this->loadModel('DnsLog');
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');

        //exec($this->kxycmd("log ImportLastDnsLog"), $output, $rc);
        $dnslogs = $this->DnsLog->find();
        $dnslogs->select([
                'domain',
                'queries_count' => $dnslogs->func()->count('*'),
                ])
            ->group('domain')
            ->order(['queries_count' => 'DESC']);

        // Set value of search Query to null by default
        $this->set('search_client_ip', null);
        $this->set('search_domain', null);
        $this->set('begin_date', null);
        $this->set('end_date', null);
        $this->set('view_type', null);
        $this->set('filter_status', null);
        $this->set('results', 25);

        if(isset($this->request->query['action']) and $this->request->query['action'] == 'search') {
            $q = null;
            if($this->request->query['begin_date'] != '') {
                // displayed requested time by user in defined app time zone
                $begin_date = $this->request->query['begin_date'];
                
                // Query database with date refers to system timezone 
                $q_begin_date = new Time($this->request->query['begin_date']);
                $q_begin_date->timezone($sys_tz_name);
                $q_begin_date = $q_begin_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $q[] = ['date_time >' => "$q_begin_date"];
            }
            if($this->request->query['end_date'] != '') {
                // displayed requested time by user in defined app time zone
                $end_date = $this->request->query['end_date'];

                // Query database with date refers to system timezone 
                $q_end_date = new Time($this->request->query['end_date']);
                $q_end_date->timezone($sys_tz_name);
                $q_end_date = $q_end_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $q[] = ['date_time <' => "$q_end_date"];
            }
            if($this->request->query['client_ip'] != '') {
                $client_ip = $this->request->query['client_ip'];
                $q[] = ['DnsLog.client_ip' => "$client_ip"];
            }
            if($this->request->query['domain'] != '') {
                $domain = $this->request->query['domain'];
                $q[] = ['DnsLog.domain LIKE' => "%$domain%"];
            }
            if($this->request->query['filter_status'] != '') {
                $filter_status = $this->request->query['filter_status'];
                $q[] = ['DnsLog.blocked' => $filter_status];
            }

            $dnslogs = $this->DnsLog->find();

            $dnslogs->select([
                    'domain',
                    'queries_count' => $dnslogs->func()->count('domain'),
                    ])
                      ->where([$q])
                    ->group('domain')
                    ->order(['queries_count' => 'DESC']);

            $this->paginate = [
                'limit' => $this->request->query['results'],
                'order' => ['DnsLog.queries_count' => 'desc']
            ];

            $this->set('search_client_ip', $this->request->query['client_ip']);
            $this->set('search_domain', $this->request->query['domain']);
            $this->set('begin_date', $this->request->query['begin_date']);
            $this->set('end_date', $this->request->query['end_date']);
            $this->set('view_type', $this->request->query['view_type']);
            $this->set('filter_status', $this->request->query['filter_status']);
            $this->set('results', $this->request->query['results']);
        } else {
            $this->paginate = [
                'limit' => 25,
                'order' => ['DnsLog.queries_count' => 'desc']
            ];
        }
        $this->loadModel('Config');
        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];
        $this->set('timezone', $timezone);

        $this->set('dnslogs', $this->paginate($dnslogs));
        $this->set('profiles', $profiles);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * This function displays charts 
     * This function can intercept GET query to filters data to display
     *
     * @return void
     */
    public function index ()
    {
        // Redirect query that depent on other view
        if(null !== $this->request->getQuery('view_type')) {
            $view_type = $this->request->getQuery('view_type');
            if($view_type != 'index') {
                return $this->redirect(['action' => $view_type, '?' => $this->request->query]);
            }
        }

        // Set list of active users and devices
        $this->loadModel('ActivesConnections');
        $active_client_options = null;
        $active_connections = $this->ActivesConnections->find('all');
        foreach($active_connections as $active_connection) {
            $active_client_options[$active_connection->ip] = $active_connection->name." (".$active_connection->ip.")";
        }
        $this->set('active_client_options', $active_client_options);

        // Set language for datetime picker
        $datetime_picker_locale = explode('_', $this->request->getSession()->read('Config.language'));
        $datetime_picker_locale = $datetime_picker_locale[0];
        $this->set('datetime_picker_locale', $datetime_picker_locale);

        // We get the system timezone offset
        $this->loadComponent('Times');
        $sys_tz_name = $this->Times->getSystemTimezone();
        $sys_tz_offset = $this->Times->getSystemTimezoneOffset();

        $app_tz_name = $this->Times->getAppTimezone();
        $app_tz_offset = $this->Times->getAppTimezoneOffset();

        $this->loadModel('DnsLog');
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');

        // By default the view show last 24 hours logs t avoid loading all logs
        // 24 hours time ago
        $time_24h_ago = new Time('24 hours ago');
        $time_24h_ago->timezone($sys_tz_name);
        $begin_date = $time_24h_ago->i18nFormat('yyyy-MM-dd HH:mm:ss');

        $cnx_logs = ConnectionManager::get('keexyboxlogs');

        // In case of charts, result is used for top values
        $results = 20;

        // Set values of search Query to null by default
        $this->set('search_client_ip', null);
        $this->set('search_domain', null);
        $this->set('begin_date', $begin_date);
        $this->set('end_date', null);
        $this->set('view_type', null);
        $this->set('filter_status', null);
        $this->set('results', $results);

        // Default Where condition
        $where_q = 'date_time > "'.$begin_date.'"';
        // Default set AND where arg for queries below that have already a where condition 
        $where_cond = 'WHERE';
        $and_where = ' AND ';

        if(null !== $this->request->getQuery('action') and $this->request->getQuery('action') == 'search') {

            $client_ip = $this->request->getQuery('client_ip');
            $domain = $this->request->getQuery('domain');

            $begin_date = $this->request->getQuery('begin_date');
            $q_begin_date = new Time($this->request->getQuery('begin_date'));
            $q_begin_date->timezone($sys_tz_name);
            $q_begin_date = $q_begin_date->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $end_date = $this->request->getQuery('end_date');
            $q_end_date = new Time($this->request->getQuery('end_date'));
            $q_end_date->timezone($sys_tz_name);
            $q_end_date = $q_end_date->i18nFormat('yyyy-MM-dd HH:mm:ss');

            $view_type = $this->request->getQuery('view_type');
            $filter_status = $this->request->getQuery('filter_status');
            $results = $this->request->getQuery('results');

            // To avoid charts display to be slow we force reasonable limit 
            if($results > 25) {
                $results = 25;
            }

            $this->set('search_client_ip', $client_ip);
            $this->set('search_domain', $domain);
            $this->set('begin_date', $begin_date);
            $this->set('end_date', $end_date);
            $this->set('view_type', $view_type);
            $this->set('filter_status', $filter_status);
            $this->set('results', $results);

            $where_array = null;
            if ($client_ip != '') { $where_array[] = "client_ip = '".$client_ip."'"; }
            if ($domain != '') { $where_array[] = "domain LIKE '%".$domain."%'"; }
            if ($begin_date != '') { $where_array[] = "date_time > '".$q_begin_date."'"; }
            if ($end_date != '') { $where_array[] = "date_time < '".$q_end_date."'"; }
            if ($filter_status != '') { $where_array[] = "blocked = '".$filter_status."'"; }

            $where_q = null;
            if($where_array != null) {
                foreach($where_array as $where_cond) {
                    $where_q = $where_q." AND ".$where_cond;
                }
                $where_cond = 'WHERE';
            } else {
                // no WHERE conditions
                $where_cond = null;
                $and_where = null;
            }
            $where_q = preg_replace('/^ AND /', '', $where_q);
        }
        $top_limit = $results;


        // Data for PIE of blocked and accepted domains
        $blocked_stat_q = '
            SELECT 
                blocked,
                COUNT(*) AS hits
            FROM
                dns_log
            WHERE    
                blocked = 1 '.$and_where.' '.$where_q.'
            GROUP BY blocked
        ';

        $accepted_stat_q = '
            SELECT 
                blocked,
                COUNT(*) AS hits
            FROM
                dns_log
            WHERE    
                blocked = 0 '.$and_where.' '.$where_q.'
            GROUP BY blocked
        ';

        $accepted_stat = $cnx_logs->execute($accepted_stat_q)->fetchAll('assoc');
        $blocked_stat = $cnx_logs->execute($blocked_stat_q)->fetchAll('assoc');

        if(isset($accepted_stat[0]['hits'])) {
            $chart_data_blocked['accepted'] = $accepted_stat[0]['hits'];
        } else {
            $chart_data_blocked['accepted'] = 0;
        }
        if(isset($blocked_stat[0]['hits'])) {
            $chart_data_blocked['blocked'] = $blocked_stat[0]['hits'];
        } else {
            $chart_data_blocked['blocked'] = 0;
        }

        // Data of most IP client
        $clients_stat_q = '
            SELECT
                client_ip,
                COUNT(*) AS hits
            FROM
                dns_log
            '.$where_cond.'    
                '.$where_q.'
            GROUP BY client_ip
            ORDER BY hits DESC
            LIMIT '.$top_limit.'
        ';

        $clients_stat = $cnx_logs->execute($clients_stat_q)->fetchAll('assoc');

        $chart_data_clients = null;
        foreach($clients_stat as $client) {
            $client_info = $this->ActivesConnections->find('all', ['conditions' => ['ip' => $client['client_ip']]])->first();
            if(isset($client_info)) {
                $chart_data_clients[] = [
                    'ip' => $client['client_ip']." (".$client_info->name.")", 
                    'hits' => $client['hits'],
                    'color' => '#'.substr(md5(rand()), 0, 6)
                ];
            } else {
                $chart_data_clients[] = [
                    'ip' => $client['client_ip'], 
                    'hits' => $client['hits'],
                    'color' => '#'.substr(md5(rand()), 0, 6)
                ];
            }
            $client_info = null;
            
        }

        // Data of most domains
        $domains_stat_q = '
            SELECT
                domain,
                COUNT(*) AS hits
            FROM
                dns_log
            '.$where_cond.'    
                '.$where_q.'
            GROUP BY domain
            ORDER BY hits DESC
            LIMIT '.$top_limit.'
        ';
        $domains_stat = $cnx_logs->execute($domains_stat_q)->fetchAll('assoc');

        $chart_data_domains = null;
        foreach($domains_stat as $domain) {
            $chart_data_domains[] = [
                'domain' => $domain['domain'], 
                'hits' => $domain['hits'],
                'color' => '#'.substr(md5(rand()), 0, 6)
            ];
        }

        ////////////////////////////////////////////////////////
        // Generate data of date time blocked/accepted charts //
        ////////////////////////////////////////////////////////
        $chart_data_datetime_blocked_q = '
            SELECT 
                DATE(CONVERT_TZ(date_time, "'.$sys_tz_offset.'", "'.$app_tz_offset.'")) AS date, 
                CONCAT(DATE_FORMAT(CONVERT_TZ(date_time, "'.$sys_tz_offset.'", "'.$app_tz_offset.'"), "%H"), ":00:00") AS time, 
                blocked, 
                COUNT(*) AS hits 
            FROM 
                dns_log 
            '.$where_cond.'    
                '.$where_q.'
            GROUP BY 
                blocked, 
                date,
                time
            ORDER BY 
            date_time ASC
        ';
        $res_chart_data_datetime_blocked = $cnx_logs->execute($chart_data_datetime_blocked_q)->fetchAll('assoc');

        $chart_data_datetime_blocked = null;
        foreach($res_chart_data_datetime_blocked as $data) {
            $key = $data['date']." ".$data['time'];
            if ($data['blocked'] == 1) {
                $chart_data_datetime_blocked[$key]['blocked'] = $data['hits'];
            } else {
                $chart_data_datetime_blocked[$key]['accepted'] = $data['hits'];
            }
            if(!isset ($chart_data_datetime_blocked[$key]['blocked'])) {
                $chart_data_datetime_blocked[$key]['blocked'] = '0';
            }
            if(!isset ($chart_data_datetime_blocked[$key]['accepted'])) {
                $chart_data_datetime_blocked[$key]['accepted'] = '0';
            }
        }

        ///////////////////////////////////////////////
        // Generate data of date time clients charts //
        ///////////////////////////////////////////////
        $client_where_q = null;
        if(isset($clients_stat)) {
            foreach($clients_stat as $client) {
                if(isset($client['client_ip'])) {
                    $client_where_q = $client_where_q.' OR client_ip = "'.$client['client_ip'].'"';
                }
            }
        }
        // Save orig where_cond and and_where values
        $old_and_where = $and_where;
        $old_where_cond = $where_cond;
        if(isset($client_where_q)) {
            $client_where_q = preg_replace('/^ OR /', '', $client_where_q);
            if(isset($where_cond)) {
                $client_where_q = '('.preg_replace('/^ OR /', '', $client_where_q).')';
                $and_where = ' AND ';
            } else {
                $where_cond = 'WHERE';
                $and_where = null;
                $client_where_q = preg_replace('/^ OR /', '', $client_where_q);
            }
        } else {
            $and_where = null;
        }

        $chart_data_datetime_clients_q = '
            SELECT 
                DATE(CONVERT_TZ(date_time, "'.$sys_tz_offset.'", "'.$app_tz_offset.'")) AS date, 
                CONCAT(DATE_FORMAT(CONVERT_TZ(date_time, "'.$sys_tz_offset.'", "'.$app_tz_offset.'"), "%H"), ":00:00") AS time, 
                client_ip, COUNT(*) AS hits 
            FROM 
                dns_log 
            '.$where_cond.'    
                '.$where_q.' '.$and_where.' '.$client_where_q.'
            GROUP BY 
                client_ip, 
                date,
                time
            ORDER BY 
            date_time ASC
        ';

        $res_chart_data_datetime_clients = $cnx_logs->execute($chart_data_datetime_clients_q)->fetchAll('assoc');
        $chart_data_datetime_clients = null;
        foreach($res_chart_data_datetime_clients as $data) {
            $key = $data['date']." ".$data['time'];
            $chart_data_datetime_clients[$key][] = ['client_ip' => $data['client_ip'], 'hits' => $data['hits']]; 
        }

        $client_labels = null;
        if(isset($chart_data_datetime_clients)) { 
            foreach($chart_data_datetime_clients as $key => $client_data) {
                $client_labels[] = $key;
            }
        }

        // Distinct client IP
        $client_list = null;
        foreach($res_chart_data_datetime_clients as $client_data) {
            $client_list[] = $client_data['client_ip'];
        }
        
        $final_client_list = null;
        if(isset($client_list)) {
            $client_list = array_unique($client_list);
        
            // initialize new Array with datetime hits values to 0 and define a HTML color for each client IP
            foreach($client_list as $client) {
                $client_info = $this->ActivesConnections->find('all', ['conditions' => ['ip' => $client]])->first();
                foreach($client_labels as $client_label) { 
                    $final_client_list[$client]['color'] = '#'.substr(md5(rand()), 0, 6);
                    if (isset($client_info['name'])) {
                        $final_client_list[$client]['legend'] = $client." (".$client_info['name'].")";
                    } else {
                        $final_client_list[$client]['legend'] = $client;
                    }
                    $final_client_list[$client]['times'][$client_label] = 0;
                }
            }

            // Populate values to Array with real get values
            foreach($chart_data_datetime_clients as $datetime => $client_items) {
                foreach($client_items as $client_data) {
                    $final_client_list[$client_data['client_ip']]['times'][$datetime] = $client_data['hits'];
                }
            }
        }

        ///////////////////////////////////////////////
        // Generate data of date time domains charts //
        ///////////////////////////////////////////////
        $domain_where_q = null;
        foreach($domains_stat as $domain) {
            if(isset($domain['domain'])) {
                $domain_where_q = $domain_where_q.' OR domain = "'.$domain['domain'].'"';
            }
        }
        // Restoring orig where_cond and and_where values
        $and_where = $old_and_where;
        $where_cond = $old_where_cond;
        if(isset($domain_where_q)) {
            $domain_where_q = preg_replace('/^ OR /', '', $domain_where_q);
            if(isset($where_cond)) {
                $domain_where_q = '('.preg_replace('/^ OR /', '', $domain_where_q).')';
                $and_where = ' AND ';
            } else {
                $where_cond = 'WHERE';
                $and_where = null;
                $domain_where_q = preg_replace('/^ OR /', '', $domain_where_q);
            }
        } else {
            $and_where = null;
        }

        $chart_data_datetime_domains_q = '
            SELECT 
                DATE(CONVERT_TZ(date_time, "'.$sys_tz_offset.'", "'.$app_tz_offset.'")) AS date, 
                CONCAT(DATE_FORMAT(CONVERT_TZ(date_time, "'.$sys_tz_offset.'", "'.$app_tz_offset.'"), "%H"), ":00:00") AS time, 
                domain, COUNT(*) AS hits 
            FROM 
                dns_log 
            '.$where_cond.'    
                '.$where_q.' '.$and_where.' '.$domain_where_q.'
            GROUP BY 
                domain, 
                date, 
                time 
            ORDER BY 
            date_time ASC
        ';

        $res_chart_data_datetime_domains = $cnx_logs->execute($chart_data_datetime_domains_q)->fetchAll('assoc');
        $chart_data_datetime_domains = null;
        foreach($res_chart_data_datetime_domains as $data) {
            $key = $data['date']." ".$data['time'];
            $chart_data_datetime_domains[$key][] = ['domain' => $data['domain'], 'hits' => $data['hits']]; 
        }

        $domain_labels = null;
        if(isset($chart_data_datetime_domains)) { 
            foreach($chart_data_datetime_domains as $key => $domain_data) {
                $domain_labels[] = $key;
            }
        }

        // Distinct domains
        $domain_list = null;
        foreach($res_chart_data_datetime_domains as $domain_data) {
            $domain_list[] = $domain_data['domain'];
        }
        
        $final_domain_list = null;
        if(isset($domain_list)) {
            $domain_list = array_unique($domain_list);
        
            // initialize new Array with datetime hits values to 0 and define a HTML color for each domain IP
            foreach($domain_list as $domain) {
                foreach($domain_labels as $domain_label) { 
                    $final_domain_list[$domain]['color'] = '#'.substr(md5(rand()), 0, 6);
                    $final_domain_list[$domain]['times'][$domain_label] = 0;
                }
            }

            // Populate values to Array with real get values
            foreach($chart_data_datetime_domains as $datetime => $domain_items) {
                foreach($domain_items as $domain_data) {
                    $final_domain_list[$domain_data['domain']]['times'][$datetime] = $domain_data['hits'];
                }
            }
        }

        $this->set('top_limit', $top_limit);
        $this->set('filtering_data', $chart_data_blocked);
        $this->set('top_domains_data', $chart_data_domains);
        $this->set('top_clients_data', $chart_data_clients);
        $this->set('datetime_blocked_data', $chart_data_datetime_blocked);
        $this->set('datetime_clients_data', $final_client_list);
        $this->set('datetime_clients_labels', $client_labels);
        $this->set('datetime_domains_data', $final_domain_list);
        $this->set('datetime_domains_labels', $domain_labels);


        $this->loadModel('Config');
        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];
        $this->set('timezone', $timezone);

        //$this->set('dnslogs', $this->paginate($dnslogs));
        $this->set('profiles', $profiles);
        $this->viewBuilder()->setLayout('adminlte');
    }
}
