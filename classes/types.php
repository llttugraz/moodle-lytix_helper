<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is a one-line short description of the file.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    lytix_helper
 * @author     Guenther Moser
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper;

/**
 * Class types
 */
class types {
    /**
     * @var TYPE_OPEN Type OPEN for the logs.
     */
    const TYPE_OPEN   = 'OPEN';
    /**
     * @var TYPE_CLOSE Type CLOSE for the logs.
     */
    const TYPE_CLOSE  = 'CLOSE';
    /**
     * @var TYPE_LOAD Type LOAD for the logs.
     */
    const TYPE_LOAD   = 'LOAD';
    /**
     * @var TYPE_UNLOAD Type UNLOAD for the logs.
     */
    const TYPE_UNLOAD = 'UNLOAD';
    /**
     * @var TYPE_EVENT Type EVENT for the logs.
     */
    const TYPE_EVENT     = 'EVENT';
    /**
     * @var TYPE_MILESTONE Type MILESTONE for the logs.
     */
    const TYPE_MILESTONE = 'MILESTONE';
    /**
     * @var TYPE_DIARY Type DIARY for the logs.
     */
    const TYPE_DIARY     = 'DIARY';
    /**
     * @var TYPE_PAGE Type PAGE for the logs.
     */
    const TYPE_PAGE      = 'PAGE';
}
