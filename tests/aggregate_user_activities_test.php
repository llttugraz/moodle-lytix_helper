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


use lytix_helper\task\aggregate_user_activities;

/**
 * Class privacy_lib_test
 * @coversDefaultClass \lytix_helper\task\aggregate_user_activities
 */
final class aggregate_user_activities_test extends \advanced_testcase {
    /**
     * Course variable.
     * @var \stdClass|null
     */
    private $course = null;

    /**
     * Weeks variable.
     * @var int
     */
    private $days = 140;

    /**
     * Variable on how much to generate.
     * @var int
     */
    private $usercount = 5;

    /**
     * Variable on how much to generate.
     * @var int
     */
    private $interactioncount = 50;

    /**
     * Variable for the students
     *
     * @var array
     */
    private $students = [];

    /**
     * Sets up course for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $start = new \DateTime($this->days . " days ago");
        $today = new \DateTime('tomorrow midnight');
        set_config('semester_start', $start->format('Y-m-d'), 'local_lytix');
        set_config('semester_end', $today->format('Y-m-d'), 'local_lytix');
        set_config('last_aggregation_date', $start->format('Y-m-d'), 'local_lytix');
        // Create course.

        $courseinfo            = new \stdClass();
        $courseinfo->fullname  = 'Test Course';
        $courseinfo->shortname = 'test_course';
        $courseinfo->enablecompletion = 1;
        $courseinfo->category  = 1;

        $this->students = dummy::create_fake_students($this->usercount);
        $return   = dummy::create_course_and_enrol_users($courseinfo, $this->students);
        $this->course = $return['course'];
        // Add course to config list.
        set_config('course_list', $this->course->id, 'local_lytix');
        // Set platform.
        set_config('platform', 'course_dashboard', 'local_lytix');
    }

    /**
     * Test get_name of task.
     * @covers ::get_name
     * @return void
     */
    public function test_task_get_name(): void {
        $task = new aggregate_user_activities();
        self::assertIsString($task->get_name(), "Aggregate user activities for Lytix");
    }

    /**
     * Test execute of task.
     * @covers ::execute
     * @covers ::check_component
     * @return void
     * @throws \dml_exception
     */
    public function test_task_execute(): void {
        global $DB;

        foreach ($this->students as $student) {
            self::simulate_interactions($student->id, $this->course->id);
        }
        $logs = $DB->get_records('logstore_standard_log', ['courseid' => $this->course->id]);
        self::assertEquals($this->usercount * $this->days * $this->interactioncount, count($logs),
            "There should be min records per day per user.");

        $task = new aggregate_user_activities();
        $task->execute();

        $dlys = $DB->get_records('lytix_helper_dly_mdl_acty', ['courseid' => $this->course->id]);
        self::assertEquals($this->usercount * $this->days, count($dlys), "");

        // Update 2024-11-05: This test is extended for cleanup (implemented in local_lytix).
        $DB->insert_record('lytix_helper_last_aggreg', ['courseid' => $this->course->id,
                'userid' => $this->students[0]->id, 'contextid' => 1, 'timestamp' => 1]);
        delete_user($this->students[0]);
        self::assertEquals(0, $DB->count_records('lytix_helper_last_aggreg'));

        delete_course($this->course->id, false);
        self::assertEquals(0, $DB->count_records('lytix_helper_dly_mdl_acty', ['courseid' => $this->course->id]));
    }

    /**
     * This function simulates a login and interactions per week.
     *
     * @param int $userid
     * @param int $courseid
     * @return void
     * @throws \dml_exception
     */
    public function simulate_interactions($userid, $courseid) {
        $start = new \DateTime(get_config('local_lytix', 'semester_start'));
        $end = new \DateTime(get_config('local_lytix', 'semester_start'));
        $end->modify('+1 day');

        for ($day = 0; $day < $this->days; $day++) {
            // Simulate logins for each week.
            self::simulate_logins($start->getTimestamp(), $end->getTimestamp(), $userid);

            // Simulate interactions for each user in the course.
            self::simulate_course_interactions($start->getTimestamp(), $end->getTimestamp(), $userid, $courseid);

            // Update the start and end dates for the next week.
            $start->modify('+1 day');
            $end->modify('+1 day');
        }
    }

    /**
     * This function simulates a login.
     *
     * @param int $startdate
     * @param int $enddate
     * @param int $userid
     * @param int $courseid
     * @return void
     */
    private function simulate_logins($startdate, $enddate, $userid, $courseid = 1) {
        $timestamp = mt_rand($startdate, $enddate);
        // Logins have no course we use the courseid 1 as default.
        self::save_interaction_to_database($userid, $courseid, $timestamp, 'core/login', 'logged in', );
    }

    /**
     * This function simulates interactions.
     *
     * @param int $startdate
     * @param int $enddate
     * @param int $userid
     * @param int $courseid
     * @return void
     */
    private function simulate_course_interactions($startdate, $enddate, $userid, $courseid) {
        $activities = [
            'core',
            'mod_forum',
            'gradereport_overview',
            'mod_url',
            'mod_book',
            'mod_scrom',
            'mod_folder',
            'mod_lesson',
            'mod_resource',
            'mod_glossary',
            'mod_lightboxgallery',
            'mod_quiz',
            'mod_bigbluebuttonbn',
            'core_h5p',
            'mod_h5pactivity',
            'mod_assign',
            'mod_workshop',
            'assignfeedback_file',
            'assignfeedback_editpdf',
            'assignfeedback_comments',
            'assignsubmission_file',
            'assignsubmission_onlinetext',
            'mod_feedback',
        ];

        for ($j = 0; $j < $this->interactioncount; $j++) {
            $timestamp = mt_rand($startdate, $enddate);
            $component = $activities[array_rand($activities)];
            // Save the interaction in the database table 'logstore_standard_log'.
            self::save_interaction_to_database($userid, $courseid, $timestamp, $component);
        }
    }

    /**
     * This function saves interactions in the logstore_standard_log table.
     * @param int $userid
     * @param int $courseid
     * @param int $timestamp
     * @param string $component
     * @return void
     * @throws \dml_exception
     */
    private function save_interaction_to_database($userid, $courseid, $timestamp, $component) {
        global $DB;

        $record = new \stdClass();
        $record->eventname = $this->generate_random_string(255);
        $record->component = $component;
        $record->action = $this->generate_random_string(100);;
        $record->target = $this->generate_random_string(100);
        $record->crud = $this->generate_random_string(1);
        $record->edulevel = random_int(0, 1);
        $record->contextid = mt_rand(1, 9223372036854775807);
        $record->contextlevel = mt_rand(1, 9223372036854775807);
        $record->contextinstanceid = mt_rand(1, 9223372036854775807);
        $record->userid = $userid;
        $record->courseid = $courseid;
        $record->anonymous = 0; // We do not use anonymous entries.
        $record->timecreated = $timestamp;

        $DB->insert_record('logstore_standard_log', $record);
    }

    /**
     * Helper function to generate a varchar string.
     *
     * @param int $length
     * @return string
     * @throws \Exception
     */
    private function generate_random_string($length) {
        $randombytes = random_bytes(floor(($length + 1) / 2));
        $randomstring = substr(bin2hex($randombytes), 0, $length);

        return $randomstring;
    }
}
