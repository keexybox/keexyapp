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

namespace keexybox;

/**
 * This component build a list of connection durations proposed to users when connecting
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class ShellComponent
{
    public function DeleteFiles($target)
    {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

            foreach( $files as $file ) {
                $this->DeleteFiles( $file );
            }

            rmdir( $target );
        } elseif(is_file($target)) {
            unlink( $target );
        }
    }
}
