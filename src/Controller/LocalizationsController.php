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
use Cake\Event\Event;
use Cake\I18n\I18n;

/**
 * This class allows users or admin to change language of Keexybox interface
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class LocalizationsController extends AppController{

    /**
     * Allow user or admin to set Language
     *
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        /* Allow user to change language when not logged in */
        $this->Auth->allow(['setlang']);
    }
    
    /**
     * Write lang in user session
     *
     * @param string $lang
     * @return void Redirects to referer
     */
    public function setlang($lang)
    {
        $this->request->session()->write('Config.language', $lang);
        return $this->redirect($this->request->referer());
    }

    /**
     * Set default language for Keexybox
     *
     * @param string $lang
     */
    public function setdefaultlang($lang)
    {
        $this->autoRender = false;
        $this->LoadModel('Config');
        $locale = $this->Config->get('locale');
        //debug($locale);
        $data = ['value' => $lang];
        if($lang == 'fr_FR' or $lang == 'en_US') {
            $newlocale = $this->Config->patchEntity($locale, $data);
            $this->Config->save($newlocale);
        }
    }
}
?>
