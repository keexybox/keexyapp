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

/**
 * ProfilesTimes Controller
 * It is only use to delete a single time, others action for Times are managed by Profiles Controller
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 * @property \App\Model\Table\ProfilesTimesTable $ProfilesTimes
 */
class ProfilesTimesController extends AppController
{
    /**
     * Delete method
     *
     * @param string|null $id Profiles Time id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $profilesTime = $this->ProfilesTimes->get($id);
        if ($this->ProfilesTimes->delete($profilesTime)) {
            $this->Flash->success(__('Connection schedule has been deleted.'));
        } else {
            $this->Flash->error(__('Connection schedule could not be deleted.')." ".__('Please try again.'));
        }
        return $this->redirect($this->referer());
    }
}
