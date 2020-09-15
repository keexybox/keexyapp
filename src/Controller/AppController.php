<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Error\Debugger;
use Cake\Core\Configure;
use Cake\I18n\I18n;
//use Cake\Controller\Component;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            // authorize to be able to manage access with isAuthorized
            'authorize' => 'Controller',
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'adminlogin'
            ],
            'loginRedirect' => [
                //'controller' => 'Connections',
                //'action' => 'index'
                'controller' => 'Users',
                'action' => 'adminlogin',
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'adminlogin',
            ]
        ]);

        $this->Cakecmd = ROOT."/bin/cake";
        $this->set('kxycmd', ROOT."/bin/cake");

        // Set Application Time Zone
        $this->loadModel('Config');
        $host_timezone = $this->Config->get('host_timezone', ['contain' => []]);
        date_default_timezone_set($host_timezone['value']);

    }

    /**
     * This function sets the CLI command
     * 
     * @param $cmd : must contains param and arguments of command
     *
     * @return CLI command path
     */
    public function kxycmd($cmd)
    {
        return ROOT."/bin/cake $cmd";
    }

    /**
     * This function sets Authorization for an admin account
     * 
     * @param $user : Username
     *
     * @return CLI command path
     */
    public function isAuthorized($user)
    {
        // Every enabled admin users get access to everything
        if (isset($user['admin']) && isset($user['enabled']) && $user['admin'] == 1 && $user['enabled'] == 1) {
           return true;
        // If user is id 1, it allow management access even if admin permission is false
        } elseif ($user['id'] == 1) {
           return true;
        }

        // By default refuse all access
        return false;
    }

    /**
     * This function will set everything is required accross the application
     * 
     * @param $event
     *
     * @return void (but set an array for the templates)
     */
    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        // Action allow without auth access. Uncomment to get access without password
        //$this->Auth->allow(['index', 'view', 'display', 'edit', 'add']);

        // Set current controller and action
        //$this->set('current_controller', $this->request->params['controller']);
        $this->set('current_controller', $this->request->getParam('controller'));
        //$this->set('current_action', $this->request->params['action']);
        $this->set('current_action', $this->request->getParam('action'));

        // Set available Languages to allow users to switch language from interface
        $this->loadComponent('Lang');
        foreach($this->Lang->ListLanguages() as $lang_code => $lang_label) {
            $languages_list["/Localizations/setlang/$lang_code"] = $lang_label;
        }
        $this->set('languages_list', $languages_list);

        // Retreive informations to show in layout
        $this->loadModel('ActivesConnections');

        $client = null;
        $ip = env('REMOTE_ADDR');

        // Get session information
        //$session = $this->request->session()->read();
        $session = $this->request->getSession()->read();

        $displayname = null;

        if(isset($session['Auth']['User'])) {
            $client['session_status'] = 'active';
        } else {
            $client['session_status'] = null;
        }
        $client['session_details'] = $session;

        // Get connection information if admin user is also connected
        $connection = $this->ActivesConnections->findByIp($ip)->contain(['Profiles'])->first();
        if(isset($connection)) {

            $client['connection_details'] = $connection->toArray();

            $connection = $this->ActivesConnections->findByIp($ip)->first();
            if ($connection->type == 'usr') {
                $this->loadModel('Users');
                $info = $this->Users->get($connection->user_id)->toArray();
                $client['connection_status'] = $client['connection_details']['status'];
            } elseif ($connection->type == 'dev') {
                $this->loadModel('Devices');
                $info = $this->Devices->get($connection->device_id)->toArray();
                $client['connection_status'] = $client['connection_details']['status'];
            } else {
                $info = null;
            }
            $client['connection_details']['client_info'] = $info;
        }

        // Check if language is defined for session
        if(isset($client['session_details']['Config']['language'])) {
            $lang = $client['session_details']['Config']['language'];
        // Else try to define language to set in session
        } else {
            // If admin session is opened, set language from Auth settings
            if (isset($client['session_details']['Auth']['User'])) {
                $lang = $client['session_details']['Auth']['User']['lang'];
            // Else determine language to set from IP
            } elseif (isset($client['connection_details']['client_info']['lang'])) {
                $lang = $client['connection_details']['client_info']['lang'];
            } else {
                $this->loadModel('Config');
                $lang = $this->Config->get('locale')->value;
            }
        }

        // Change few values to display in the Layout
        if(isset($client['connection_details']['type'])){
            if ($client['connection_details']['type'] == 'usr') {
                $client['connection_details']['type'] = 'User';
            } elseif ($client['connection_details']['type'] == 'dev') {
                $client['connection_details']['type'] = 'Device';
            }
        }

        if(isset($client['connection_details']['profile']['default_routing'])) {
            if ($client['connection_details']['profile']['default_routing'] == 'direct') {
                $client['connection_details']['profile']['default_routing'] = 'Direct';
            } elseif ($client['connection_details']['profile']['default_routing'] == 'tor') {
                $client['connection_details']['profile']['default_routing'] = 'Tor';
            }
        }

        // Write defined langage to session
        //$this->request->session()->write('Config.language', $lang);
        $this->request->getSession()->write('Config.language', $lang);

        // define short format of lang used by bootstrap datapicker
        $short_lang = explode('_', $lang);
        $short_lang = $short_lang[0];

        // Set language
        //I18n::locale($lang);
        I18n::setLocale($lang);

        // Set langage to show in the views
        $this->set('lang', $lang);
        $this->set('short_lang', $short_lang);

        // Set client information usable for all views
        $this->set('lo_client', $client);
    }
}
