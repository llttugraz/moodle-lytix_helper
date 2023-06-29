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
 * @package    lytix_helper
 * @author     Guenther Moser
 * @copyright  2021 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper;

/**
 * Class for the planner forms.
 */
class forms_helper {

    /**
     * Retrun the year of the semester start.
     * @return false|mixed|string
     * @throws \dml_exception
     */
    public static function get_semester_start_year() {
        $datestring = get_config('local_lytix', 'semester_start');
        $startyear  = explode('-', $datestring);

        return reset($startyear);
    }

    /**
     * Retrun the year of the semester end.
     * @return false|mixed|string
     * @throws \dml_exception
     */
    public static function get_semester_end_year() {
        $datestring = get_config('local_lytix', 'semester_end');
        $endyear = explode('-', $datestring);

        return reset($endyear);
    }
}
