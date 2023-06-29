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
 * Choose and download exam backups
 *
 * @package    lytix_helper
 * @author     Günther Moser <moser@tugraz.at>
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper;

/**
 * Class privacy_lib_test
 * @coversDefaultClass \lytix_helper\forms_helper
 */
class forms_helper_test extends \advanced_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Testing the helper get_semester_start_year function.
     * @covers ::get_semester_start_year
     * @return void
     * @throws \dml_exception
     */
    public function test_get_semester_start_year() {
        $now = new \DateTime('now');
        set_config('semester_start', $now->format('Y-m-d'), 'local_lytix');
        $year = forms_helper::get_semester_start_year();
        self::assertEquals($now->format('Y'), $year);
    }

    /**
     * Testing the helper get_semester_end_year function.
     * @covers ::get_semester_end_year
     * @return void
     * @throws \dml_exception
     */
    public function test_get_semester_end_year() {
        $now = new \DateTime('now');
        set_config('semester_end', $now->format('Y-m-d'), 'local_lytix');
        $year = forms_helper::get_semester_end_year();
        self::assertEquals($now->format('Y'), $year);
    }
}
