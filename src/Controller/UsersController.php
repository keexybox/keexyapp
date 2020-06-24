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

use Cake\Cache\Cache;
use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

/**
 * This class allows to manage users
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class UsersController extends AppController
{
    /**
     * This function set pages that are available without an authentication
     *
     * @return void
     *
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Allowed page as user
        $allowed_pages = ['login', 'logout', 'adminlogin', 'disconnect'];

        $this->loadModel('Config');
        // Allow user to access register page
        $cportal_allow_register = $this->Config->get('cportal_allow_register')->value;
        if ($cportal_allow_register == 1) {
            array_push($allowed_pages, 'register');
        }

        // Allow user to access fast login page
        $cportal_fast_login = $this->Config->get('cportal_fast_login')->value;
        if ($cportal_fast_login == 1) {
            array_push($allowed_pages, 'fastlogin');
        }

        //No login required for following pages
        $this->Auth->allow($allowed_pages);
    }

    /**
     * List users and bulk management of users
     *
     * @return void
     */
    public function index()
    {

        if(null !== $this->request->getQuery('sort')) {
            $users = $this->Users->find('all')->contain(['Profiles']);
        } else {
            $users = $this->Users->find('all')->contain(['Profiles'])->order(['username']);
        }

        // Set value of search Query to null by default
        $this->set('search_query', null);

        if(null !== $this->request->getQuery('query')) {
            if($this->request->getQuery('action') == 'search') {
                $query = $this->request->getQuery('query');
                $users = $this->Users->find()
                        ->where(['OR' => ['Users.username LIKE' => "%$query%", 'Profiles.profilename LIKE' => "%$query%"]])
                        //->orWhere(['Profiles.profilename LIKE' => "%$query%"])
                        ->contain(['Profiles']);

                $this->paginate = [
                    'contain' => ['Profiles'],
                    'limit' => $this->request->getQuery('results'),
                ];

                // Set value of search Query to show search in view result
                $this->set('search_query', $query);
            }
        } else {
            $this->paginate = [
                'contain' => ['Profiles'],
                'limit' => 25
            ];
        }

        if ($this->request->is('post')) {

            if(isset($this->request->data['check'])) {
                if($this->request->data['action'] == 'disable') {
                    if(isset($this->request->data['check'])) {
                        foreach($this->request->data['check'] as $id) {
                            $user = $this->Users->get($id);
                            $data = ['enabled' => false];
                            $this->Users->patchEntity($user, $data);
                            $this->Users->save($user);
                        }
                    }
                }
    
                if($this->request->data['action'] == 'enable') {
                    if(isset($this->request->data['check'])) {
                        foreach($this->request->data['check'] as $id) {
                            $user = $this->Users->get($id);
                                $data = ['enabled' => true];
                                $this->Users->patchEntity($user, $data);
                                $this->Users->save($user);
                        }
                    }
                }
    
                if($this->request->data['action'] == 'setprofile') {
                    if($this->request->data['profile_id'] != '') {
                        if(isset($this->request->data['check'])) {
                            foreach($this->request->data['check'] as $id) {
                                if(isset($this->request->data['profile_id'])) {
                                    $user = $this->Users->get($id);
                                    $data = ['profile_id' => $this->request->data['profile_id']];
                                    $this->Users->patchEntity($user, $data);
                                    $this->Users->save($user);
                                }
                            }
                        }
                    } else {
                        $this->Flash->warning(__('Please select a profile.'));
                    }
                }
    
                if($this->request->data['action'] == 'delete') {
                    if(isset($this->request->data['check'])) {
                        foreach($this->request->data['check'] as $id) {
                            $user = $this->Users->get($id);
                            if($id != 1) {
                                if ($this->Users->delete($user)) {
                                    $this->disconnectuser($user['username']);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->Flash->warning(__('Nothing was selected.'));
            }
        }

        $this->set('users', $this->paginate($users));
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');
        $this->set('profiles',$profiles);
        $this->set('_serialize', ['users']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Add user 
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Get available profiles
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');

        // Set list of availables languages
        $this->loadComponent('Lang');
        $this->set('langs', $this->Lang->ListLanguages());

        // Get default language to sugest if as default language
        $this->loadModel('Config');
        $locale = $this->Config->get('locale')->value;
        // Create new user object
        $user = $this->Users->newEntity();
        // preset language
        $user->lang = $locale;

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been added successfully.'));

                // Only use for wizard config
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (null !== $this->request->getQuery('install_type')) {
                    $install_type = $this->request->getQuery('install_type');
                }

                if ($run_wizard->value == 1) {
                    return $this->redirect(['action' => 'wadd', 'install_type' => $install_type]);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('Unable to add the user.'));
        }

        $this->set('profiles',$profiles);
        $this->set('user', $user);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Add user 
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function wadd()
    {
        $this->add();
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * Register - allows user to register himself
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        // Get available profiles
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');

        // Set list of availables languages
        $this->loadComponent('Lang');
        $this->set('langs', $this->Lang->ListLanguages());

        // Get default language to sugest if as default language
        $this->loadModel('Config');
        $locale = $this->Config->get('locale')->value;
        // Create new user object
        $user = $this->Users->newEntity();
        // preset language
        $user->lang = $locale;

        if ($this->request->is('post')) {
            $user_data = $this->request->getData();
            debug($user_data);

            $user = $this->Users->patchEntity($user, $this->request->data);
            /*
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been added successfully.'));

                // Only use for wizard config
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (null !== $this->request->getQuery('install_type')) {
                    $install_type = $this->request->getQuery('install_type');
                }

                if ($run_wizard->value == 1) {
                    return $this->redirect(['action' => 'wadd', 'install_type' => $install_type]);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('Unable to add the user.'));
            */
        }

        $this->set('profiles',$profiles);
        $this->set('user', $user);
        $this->viewBuilder()->setLayout('wizard');
    }

    /*
     * Edit user settings
     *
     * @param id : user id
     * 
     * @return void
     */
    public function edit($id = null)
    {
        $this->loadModel('Profiles');
        $this->loadComponent('Lang');

        // Set list of profiles
        $profiles = $this->Profiles->find('list');
        $this->set('profiles',$profiles);

        // Set list of availables languages
        $this->set('langs', $this->Lang->ListLanguages());

        $user = $this->Users->get($id);

        if ($this->request->is(['post', 'put'])) {

            $old_profile_id = $user['profile_id'];

            // Update password if a new one has been entered
            if($this->request->data['new_password'] != '') {
                $this->request->data['password'] = $this->request->data['new_password'];
                $this->request->data['confirm_password'] = $this->request->data['new_confirm_password'];
                unset($this->request->data['new_password']);
                unset($this->request->data['new_confirm_password']);
            } else {
                unset($this->request->data['new_password']);
                unset($this->request->data['new_confirm_password']);
            }

            // If an updated is done for user id 1, admin access and account are forced to enable
            if($user['id'] == 1) {
                $this->request->data['enable'] = 1;
                $this->request->data['admin'] = 1;
            }

            $this->Users->patchEntity($user, $this->request->data);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been updated successfully.'));
                if($user['profile_id'] != $old_profile_id) {
                    $this->Flash->set(__('Do you want to reconnect {0} with new profile?', $user['username']), [
                            'key' => 'reconnect',
                            'element' => 'reconnect_link', 
                            'params' => [ 'reconnectlink' => '/users/reconnectuser/'.$user['username']]
                    ]);
                }
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Unable to update the user.'));
            }
        }

        // Tranfert result to the View
        $this->set('user', $user);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /*
     * Delete a user
     *
     * @param id : user id
     * 
     * @return void
     */
    public function delete($id)
    {
        // Allow only HTTP methode POST DELETE
        $this->request->allowMethod(['post', 'delete']);

        //Load user to delete
        $user = $this->Users->get($id);
        if($id != 1) {
            //Delete user
            if ($this->Users->delete($user)) {
            //Message on success
                if($this->disconnectuser($user['username'])) {
                    $this->Flash->success(__("The user {0} has been deleted.", h($user['username'])));
                    return $this->redirect(['action' => 'index']);
                }
            }    
        } else {
            $this->Flash->error(__('Deleting the user {0} is forbidden.', h($user['username'])));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Export users to downloadable CSV file
     *
     * @param null
     * @return CSV File
     */
    public function export() {
            $this->autoRender = false;
            $users = $this->Users->find('all')->contain(['Profiles']);

            // Create output file
            $fp = fopen('php://output', 'w');
            $csv_file = "keexybox_users.csv";

            // Set Headers
            $this->response->header('Content-Type', 'application/csv');
            $this->response->header('Content-Disposition', "attachment; filename=$csv_file");

            // Delimiter
            $d=";";
            // Enclosure
            $e='"';

            // insert header to CSV file
            fputs($fp, $e.'username'.$e.$d.$e.'displayname'.$e.$d.$e.'password'.$e.$d.$e.'profilename'.$e.$d.$e.'lang'.$e.$d.$e.'enabled'.$e.$d.$e.'admin'.$e."\n");

            // CSV DATA
            foreach ($users as $user) {
                fputs($fp, $e.$user->username.$e.$d.$e.$user->displayname.$e.$d.$e.$user->password.$e.$d.$e.$user->profile->profilename.$e.$d.$e.$user->lang.$e.$d.$user->enabled.$d.$user->admin."\n");
            }
    }

    /**
     * Import users from CSV file
     *
     * @param null
     * @return void Redirects to index
     */
    public function import()
    {
        if ($this->request->is('post')) {

            $this->loadModel('Profiles');
            // Load model that allow to import Users without hashing the password
            $this->loadModel('UsersNoHashPassword');

            // Save file on webroot/upload/
            if(move_uploaded_file($this->request->data['file']['tmp_name'], "upload/".$this->request->data['file']['name']))
            {
                // set CSV file
                $csv_file = WWW_ROOT."upload/".$this->request->data['file']['name'];

                // delimiter
                $d=";";
                // Enclosure
                $e='"';

                // Remove Windows ending lines
                $file = file_get_contents($csv_file);
                $file = str_replace("\r", "", $file);
                file_put_contents($csv_file, $file);

                // Open file
                $fp = fopen($csv_file, "r");

                $csv_data = [];
                while ($data = fgetcsv($fp, 0, $d, $e)) {
                    $csv_data[] = $data;
                }

                // Check if CSV contains right headers
                $csv_headers = $csv_data[0];

                // Value to store import errors from CSV
                $import_errors = [];

                // import status, use to Flash message
                //  0 = ok, 1 = warning, else = critical
                $import_status = 0;

                // Conditionnal import job
                foreach ($csv_data as $key=>$csv_line) {
                    // if csv line contains 7 field we allow import
                    if (count($csv_line) == 7) {
                        if ($key == 0) {
                            // Check header
                            $check_res = 0;
                            if ($csv_line[0] != 'username') { $check_res++; }
                            if ($csv_line[1] != 'displayname') { $check_res++; }
                            if ($csv_line[2] != 'password') { $check_res++; }
                            if ($csv_line[3] != 'profilename') { $check_res++; }
                            if ($csv_line[4] != 'lang') { $check_res++; }
                            if ($csv_line[5] != 'enabled') { $check_res++; }
                            if ($csv_line[6] != 'admin') { $check_res++; }

                            // If headers are not ok, stop import
                            if( $check_res != 0 ) {
                                $import_status = 2;
                                break;
                            }
                        } else {
                            // Set user name
                            $user_data['username'] = $csv_line[0];
    
                            // Set display name
                            $user_data['displayname'] = $csv_line[1];
    
                            // Set password
                            $user_data['password'] = $csv_line[2];
                            if (strlen($user_data['password']) == 60) {
                                $users_controller = 'UsersNoHashPassword';
                            } else {
                                $users_controller = 'Users';
                            }
    
                            // Set profile ID
                            $profilename = $csv_line[3];
                            $profile = $this->Profiles->findByProfilename($profilename)->first();
                            // If profile name does not exist set users to default profile
                            if ($profile['id'] == null) {
                                $user_data['profile_id'] = 1;
                            } else {
                                $user_data['profile_id'] = $profile['id'];
                            }

                            // Set if user will be enabled
                            $user_data['lang'] = $csv_line[4];

                            // Set if user will be enabled
                            $user_data['enabled'] = $csv_line[5];
    
                            // Set if user will be enabled
                            $user_data['admin'] = $csv_line[6];
        
                            //Check if username exist
                            $user = $this->$users_controller->findByUsername($user_data['username'])->first();
                            // if not exists, add a new one
                            if ($user == null) {
                                $user = $this->$users_controller->newEntity();
                                $user = $this->$users_controller->patchEntity($user, $user_data);
                                if ( ! $this->$users_controller->save($user)) {
                                    $import_errors[] = __('Import error line number {0}.', $key + 1);
                                }
                            // Else update existing one
                            } else {
                                //Update user
                                //Force to keep default admin account active and avoid mistake with password reset
                                if($user['id'] == 1) {
                                    $user_data['enabled'] = 1;
                                    $user_data['admin'] = 1;
                                    unset($user_data['password']);
                                }
                                $user = $this->$users_controller->patchEntity($user, $user_data);
                                if ( ! $this->$users_controller->save($user)) {
                                    $import_errors[] = __('Import error line number {0}.', $key + 1);
                                }
                            }
                        }
                    } else {
                        $import_errors[] = __('Import error line number {0}.', $key + 1);
                    }
                }

                // If import error, set import status to warning
                if (count($import_errors) != 0) {
                    $import_status = 1;
                }

                // Flash final message
                if ($import_status == 0) {
                    $this->Flash->success(__('Users have been imported successfully.'));
                    return $this->redirect(['action' => 'index']);

                } elseif ($import_status == 1){
                    $this->Flash->warning(__('Users have been imported with errors.'));
                    $errors = null;
                    foreach($import_errors as $import_error) {
                        $errors = $import_error."\n";
                        $this->Flash->set($errors, [
                            'key' => 'import_log',
                            'element' => 'log_message', 
                        ]);
                    }
                } else {
                    $this->Flash->error(__('Users not imported.')." ".__('Bad CSV headers.'));
                }

                // delete CSV file
                unlink($csv_file);

            } else {
                $this->Flash->error(__('Unable to upload file.')." ".__('Please try again.'));
            }
        }

        $this->viewBuilder()->setLayout('adminlte');

    }

    /**
     * Authenticate user and connect him to Internet (by running $this->connect() method) 
     *
     * @return void
     */
    public function login()
    {
        // check if user is already connected and redirect to ActivesConnections/view
        $this->loadModel('ActivesConnections');
        $ip = env('REMOTE_ADDR');
        $active_connection = $this->ActivesConnections->find('all', ['conditions' => [
            'ip' => $ip
        ]])->first();
        if(isset($active_connection)) {
            return $this->redirect(['controller' => 'Connections', 'action' => 'view']);
        }

        $this->loadModel('Config');
        $connection_default_time = $this->Config->get('connection_default_time');
        $connection_max_time = $this->Config->get('connection_max_time');
        $cportal_allow_register = $this->Config->get('cportal_allow_register')->value;

        $this->loadComponent('ConnectionDuration');
        $duration_list = $this->ConnectionDuration->GetDurationList();

        // Login and connection process
        if ($this->request->is('post')) {
            
            // Identify user
            $user = $this->Auth->identify();
            // If user identified
            if ($user) {
                // Connect user to internet
                $username = $this->request->data['username'];
                $session_time = $this->request->data['sessiontime'];

                $this->connect($username, $session_time);

                return $this->redirect(['controller' => 'Connections', 'action' => 'view']);
            } else {
                $this->Flash->error(__('Incorrect login or password.')." ".__('Please try again.'));
            }
        }
        //$this->set('lang', $this->lang);
        $this->set('connection_default_time', $connection_default_time);
        $this->set('connection_max_time', $connection_max_time);
        $this->set('duration_list', $duration_list);
        $this->set('cportal_allow_register', $cportal_allow_register);
        $this->viewBuilder()->setLayout('loginlte');
    }

    /**
     * Authenticate admin user to manage Keexybox
     *
     * @return void
     */
    public function adminlogin()
    {
        //Check if user is already connected and redirect
        if ($this->Auth->user()) {
            return $this->redirect(['controller' => 'statistics', 'action' => 'index']);
        }
        if ($this->request->is('post')) {
            
            $username = $this->request->data['username'];
            $user_info = $this->Users->findByUsername($username)->first();
            if(isset($user_info)) {
                if($user_info->admin == true || $user_info->id == 1) {
                    // Identify user
                    $user = $this->Auth->identify();
                    // If user identified
                    if ($user) {
                        //Set session for user
                        $this->Auth->setUser($user);

                        // Check if wizard must be run to setup Keexybox
                        $this->loadModel('Config');
                        $run_wizard = null;
                        $run_wizard = $this->Config->get('run_wizard');
        
                        // Redirect to wizard else to Statistics
                        if ($run_wizard->value == 1)  {
                            return $this->redirect(['controller' => 'config', 'action' => 'wstart']);
                        } else {
                            return $this->redirect(['controller' => 'statistics', 'action' => 'index']);
                        }
                    }
                    // Else show error
                    $this->Flash->error(__("Incorrect login or password.")." ".__("Please try again."));
                } else {
                    $this->Flash->error(__("You do not have management permissions."));
                }
            } else {
                // Else show error
                $this->Flash->error(__("Incorrect login or password.")." ".__("Please try again."));
            }
        }
        //$this->set('lang', $this->lang);
        $this->viewBuilder()->setLayout('loginlte');
    }

    /**
     * Logout admin user
     *
     * @return void
     */
    public function logout()
    {
        $this->Auth->logout();
        return $this->redirect(["action" => "adminlogin"]);
    }

    /**
     * Connect user to network. This method is called on successful $this->login() method
     *
     * @param username     : login name
     * @param session_time : duration of connection session
     * 
     * @return void
     */
    public function connect($username, $session_time)
    {
        $this->autoRender = false;

        $ip = env('REMOTE_ADDR');
        exec($this->kxycmd("users connect $username $ip $session_time"), $output, $rc);

        if($rc == 0) {
            $this->Flash->success(__("You are now connected to the Internet."));
        } else {
            $this->Flash->error(__("Unable to connect you to the Internet."));
        }

        return $this->redirect($this->referer());
    }

    /**
     * Disconnect a user who initiated the disconnection
     *
     * @return void
     */
    public function disconnect()
    {
        $this->autoRender = false;
        $this->loadModel('ActivesConnections');

        $ip = env('REMOTE_ADDR');

        $activeuser = $this->ActivesConnections->find('all', [ 'conditions' => [ 'ActivesConnections.ip' => $ip ]])->first();

        // User in pause set by administrator can't disconnect 
        if($activeuser->status == 'running') {
            exec($this->kxycmd("users disconnect ".$activeuser['name']." ".$activeuser['ip']), $output, $rc);
            if($rc == 0) {
                $this->Flash->success(__("You are now disconnected from the internet."));
            } else {
                $this->Flash->error(__("Unable to disconnect you from the Internet."));
            }
        }
        elseif($activeuser->status == 'pause') {
            $this->Flash->error(__("Your connection is paused.")." ".__("Disconnections are forbidden for paused connections."));
        }

        return $this->redirect(['action' => 'login']);

    }

    /**
     * Disconnect a user initiated by admin user
     *
     * @param username : login name
     * @param ip       : ip used by user
     *
     * @return void
     */
    public function disconnectuser($username, $ip = null)
    {
        $this->autoRender = false;

        if(isset($ip)) {
            // this disconnect user connected from ip
            exec($this->kxycmd("users disconnect $username $ip"), $output, $rc);
        } else {
            // this disconnect user everywhere is connected
            exec($this->kxycmd("users disconnect $username"), $output, $rc);
        }

        if($rc == 0) {
            $this->Flash->success(__("The user {0} has been disconnected successfully.", h($username)));
        } else {
            $this->Flash->error(__("Unable to disconnect the user {0}.", h($username)));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Pause a user connection initiated by admin user
     *
     * @param username : login name
     * @param ip       : ip used by user
     * 
     * @return void
     */
    public function pauseuser($username, $ip = null)
    {
        $this->autoRender = false;

        if(isset($ip)) {
            // this disconnect a single ip used by a user
            exec($this->kxycmd("users pause $username $ip"), $output, $rc);
        } else {
            // this disconnect every ip used by user 
            exec($this->kxycmd("users pause $username"), $output, $rc);
        }

        if($rc == 0) {
            $this->Flash->success(__("The connection of the user {0} has been paused.", h($username)));
        } else {
            $this->Flash->error(__("Unable to pause the connection of the user {0}.", h($username)));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Resume a user connection initiated by admin user
     *
     * @param username : login name
     * @param ip       : ip used by user
     * 
     * @return void
     */
    public function runuser($username, $ip = null)
    {
            $this->autoRender = false;

        if(isset($ip)) {
            // this disconnect user connected from ip
            exec($this->kxycmd("users run $username $ip"), $output, $rc);
        } else {
            // this disconnect user everywhere is connected
            exec($this->kxycmd("users run $username"), $output, $rc);
        }

        if($rc == 0) {
            $this->Flash->success(__("The connection of the user {0} has been resumed.", h($username)));
        } else {
            $this->Flash->error(__("Unable to resume the connection of the user {0}.", h($username)));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Reconnect a user initiated by admin user
     *
     * For example, this is use when profile changed for a user 
     * and admin wants to reconnect him with new profile
     *
     * @param username : login name
     * @param ip       : ip used by user
     * 
     * @return void
     */
    public function reconnectuser($username, $ip = null)
    {
        $this->autoRender = false;
        if(isset($ip)) {
            exec($this->kxycmd("users reconnect $username $ip"), $output, $rc);
        } else {
            exec($this->kxycmd("users reconnect $username"), $output, $rc);
        }

        if($rc == 0) {
            $this->Flash->success(__("The user {0} has been reconnected successfully.", h($username)));
        } else {
            $this->Flash->error(__("Unable to reconnect the user {0}.", h($username)));
        }

        return $this->redirect($this->referer());
    }
}
