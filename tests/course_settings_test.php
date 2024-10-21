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
 * @coversDefaultClass \lytix_helper\course_settings
 */
final class course_settings_test extends \advanced_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Testing the helper getcoursestartdate function.
     * @covers ::getcoursestartdate
     * @return void
     * @throws \dml_exception
     */
    public function test_getcoursestartdate(): void {
        // Course has no date, use semester date.
        $now = new \DateTime('now');
        set_config('semester_start', $now->format('Y-m-d'), 'local_lytix');
        $course = $this->getDataGenerator()->create_course();
        $tmp = course_settings::getcoursestartdate($course->id);
        self::assertEquals($now->format('Y-m-d'), $tmp->format('Y-m-d'));

        // Course has date, but semester is higher.
        $course->starttime = $now->getTimestamp();
        $now = new \DateTime('now');
        $now->modify('+1 month');
        set_config('semester_start', $now->format('Y-m-d'), 'local_lytix');
        $tmp = course_settings::getcoursestartdate($course->id);
        self::assertEquals($now->format('Y-m-d'), $tmp->format('Y-m-d'));
    }

    /**
     * Testing the helper getcoursestartdate function.
     * @covers ::getcourseenddate
     * @return void
     * @throws \dml_exception
     */
    public function test_getcourseenddate(): void {
        // Course has no date, use semester date.
        $now = new \DateTime('now');
        set_config('semester_end', $now->format('Y-m-d'), 'local_lytix');
        $course = $this->getDataGenerator()->create_course();
        $tmp = course_settings::getcourseenddate($course->id);
        self::assertEquals($now->format('Y-m-d'), $tmp->format('Y-m-d'));

        // Course has date, but is higher.
        $now2 = new \DateTime('now');
        $now2->modify('+1 month');
        $course->enddate = $now2->getTimestamp();
        $tmp = course_settings::getcourseenddate($course->id);
        self::assertEquals($tmp->format('Y-m-d'), $now->format('Y-m-d'));
    }
}
