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

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * This component build a list of available languages for Keexybox web interface
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class LangComponent extends Component
{
    public function ListLanguages()
    {
        $languages = [
            'en_US' => 'English',
            'zh_CHS' => '中文',
            'de_DE' => 'Deutsch',
            'es_ES' => 'Español',
            'fr_FR' => 'Français',
            'el_GR' => 'ελληνικά',
            'it_IT' => 'Italiano',
            'ja_JP' => '日本語',
            'pl_PL' => 'Polski',
            'pt_PT' => 'Português',
            'ru_RU' => 'Pусский',
            ];
        return $languages;
    }
}
