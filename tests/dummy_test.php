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

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

use mod_quiz\plugininfo\quiz;
use mod_quiz\quiz_attempt;

/**
 * Class privacy_lib_test
 * @coversDefaultClass \lytix_helper\dummy
 */
class dummy_test extends \advanced_testcase {
    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test test_create_fake_students
     *
     * @covers ::create_fake_students
     * @return void
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function test_create_fake_students() {

        $students = dummy::create_fake_students(0);
        self::assertIsArray($students, "Shold return an array.");
        self::assertEmpty($students, "Array shold be empty.");

        $students = dummy::create_fake_students(10);
        self::assertIsArray($students, "Should retrun an array");
        self::assertEquals(10, count($students), "There shoud be 10 objects in array.");
    }

    /**
     * Test create_course_and_enrol_users
     *
     * @covers ::create_course_and_enrol_users
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function test_create_course_and_enrol_users() {
        $course            = new \stdClass();
        $course->fullname  = 'Test Course';
        $course->shortname = 'test_course';
        $course->category  = 1;

        $students = dummy::create_fake_students(2);
        $return   = dummy::create_course_and_enrol_users($course, $students);
        $course   = $return['course'];
        $student  = $return['student0'];

        self::assertIsArray($return, "Retrun shoud be an array.");
        self::assertEquals(2, count($return), "Array should have 2 entires.");
        self::assertIsObject($course, "There should be an course object.");
        self::assertIsObject($student, "There should be an student object.");
    }

    /**
     * Test create_fake_data_for_course
     *
     * @covers ::create_fake_data_for_course
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function test_create_fake_data_for_course() {
        global $DB;

        // Create needed data and variables.
        $date = new \DateTime('5 months ago');
        date_add($date, date_interval_create_from_date_string('6 hours'));
        $today = new \DateTime('today midnight');
        date_add($today, date_interval_create_from_date_string('6 hours'));

        $student = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $course->startdate = $date->getTimestamp();
        $course->enddate   = $today->getTimestamp();
        $context = \context_course::instance($course->id);

        // Table should be empty.
        $records = $DB->get_records('lytix_helper_dly_mdl_acty', ['userid' => $student->id, 'courseid' => $course->id]);
        self::assertIsArray($records, "Shold be an array.");
        self::assertEmpty($records, "Should be empty.");

        // Fill table and check again.
        dummy::create_fake_data_for_course($date, $today, $student, $course->id, $context);
        $records = $DB->get_records('lytix_helper_dly_mdl_acty', ['userid' => $student->id, 'courseid' => $course->id]);
        self::assertIsArray($records, "Should be an array");
        self::assertNotEmpty($records);
    }

    /**
     * Test create_fake_planner_event
     *
     * @covers ::create_fake_planner_event
     * @return void
     * @throws \dml_exception
     */
    public function test_create_fake_planner_event() {
        global $DB;
        // Create data.
        $course = $this->getDataGenerator()->create_course();
        $today = new \DateTime('now');
        date_add($today, date_interval_create_from_date_string('2 days'));
        // Table should be empty.
        $records = $DB->get_records('lytix_planner_events', ['courseid' => $course->id]);
        self::assertIsArray($records, "Shold be an array.");
        self::assertEmpty($records, "Should be empty.");
        // Fill table and check again.
        dummy::create_fake_planner_event($course, 'Lecture_Vorlesung', 'L', $today->getTimestamp(),
            $today->getTimestamp(), 'Lecture 1', 'Text 1', 'HS G', 1, 0, 0, 0);
        $records = $DB->get_records('lytix_planner_events', ['courseid' => $course->id]);
        self::assertIsArray($records, "Should be an array");
        self::assertNotEmpty($records);
    }

    /**
     * Test complete_fake_planner_event
     *
     * @covers ::complete_fake_planner_event
     * @return void
     * @throws \dml_exception
     */
    public function test_complete_fake_planner_event() {
        global $DB;
        // Create data.
        $today = new \DateTime('now');
        date_add($today, date_interval_create_from_date_string('2 days'));
        $student = $this->getDataGenerator()->create_user(['role' => 'student']);
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($student->id, $course->id);
        $event = dummy::create_fake_planner_event($course, 'Lecture_Vorlesung', 'L', $today->getTimestamp(),
            $today->getTimestamp(), 'Lecture 1', 'Text 1', 'HS G', 1, 0, 0, 0);
        // Table should be empty.
        $records = $DB->get_records('lytix_planner_event_comp', ['courseid' => $course->id]);
        self::assertIsArray($records, "Shold be an array.");
        self::assertEmpty($records, "Should be empty.");
        // Fill table and check again.
        dummy::complete_fake_planner_event($event->id, $course->id, $student->id,
            1, 0, (new \DateTime('now'))->getTimestamp());
        $record = $DB->get_record('lytix_planner_event_comp', ['eventid' => $event->id, 'courseid' => $course->id]);
        self::assertIsObject($record, "Should be an object.");
        self::assertTrue((boolean) $record->completed, "Should be completed.");
    }

    /**
     * Test create_update_fake_planner_milestone and update_fake_planner_milestone
     *
     * @covers ::create_fake_planner_milestone
     * @covers ::update_fake_planner_milestone
     * @return void
     * @throws \dml_exception
     */
    public function test_create_update_fake_planner_milestone() {
        global $DB;
        // Create data.
        $student = $this->getDataGenerator()->create_user(['role' => 'student']);
        $course = $this->getDataGenerator()->create_course();
        $date = new \DateTime('now');
        $date->modify('+3 days');

        // Table should be empty.
        $records = $DB->get_records('lytix_planner_milestone', ['courseid' => $course->id, 'userid' => $student->id]);
        self::assertIsArray($records, "Shold be an array.");
        self::assertEmpty($records, "Should be empty.");
        // Fill table and check again.
        $mlstn = dummy::create_fake_planner_milestone($course, $student, 'Milestone', 'M', $date->getTimestamp(),
            $date->getTimestamp(), 'Title', 'Text', 3, 'email', 0, 0);
        $record = $DB->get_record('lytix_planner_milestone', ['id' => $mlstn->id]);
        self::assertIsObject($record, "Should be an object.");
        // Change mlst and check.
        $mlstn->completed = 1;
        dummy::update_fake_planner_milestone($mlstn);
        $record = $DB->get_record('lytix_planner_milestone', ['id' => $mlstn->id]);
        self::assertIsObject($record, "Should be an object.");
        self::assertTrue((boolean) $record->completed, "Should be completed.");
    }

    /**
     * Test create quiz, quiz question and quiz attempt.
     *
     * @covers ::create_quiz
     * @covers ::create_quiz_question
     * @covers ::create_quiz_attempt
     * @covers ::finish_quiz_attempt
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function test_quiz() {
        // Create data.
        $start = new \DateTime('5 months ago');
        date_add($start, date_interval_create_from_date_string('6 hours'));
        $today = new \DateTime('today midnight');
        date_add($today, date_interval_create_from_date_string('6 hours'));

        $student = $this->getDataGenerator()->create_user(['role' => 'student']);
        $course = $this->getDataGenerator()->create_course(
            ['enablecompletion' => 1, 'startdate' => $start->getTimestamp(), 'enddate' => $today->getTimestamp()]);

        // Create a quiz.
        $quiz = dummy::create_quiz($course);

        // Check the quiz.
        self::assertIsObject($quiz, "Should be an object.");

        // Create a numerical question.
        $quiz = dummy::create_quiz_question($quiz);

        // Check the numerical question.
        self::assertIsObject($quiz, "Should be an object.");

        // Start the passing attempt.
        $timenow = time();
        $attempt = dummy::create_quiz_attempt($quiz, $student, $timenow);

        // Check the passing attempt.
        self::assertIsObject($attempt, "Should be an object.");

        // Finish the passing attempt.
        dummy::finish_quiz_attempt($attempt, $timenow, '3.14');

        // Check the finished attempt.
        self::assertIsObject($attempt, "Should be an object.");

        // Check that results are stored as expected.
        $this->assertEquals(1, $attempt->get_attempt_number());
        $this->assertEquals(1, $attempt->get_sum_marks());
        $this->assertEquals(true, $attempt->is_finished());
        $this->assertEquals($timenow, $attempt->get_submitted_date());
        $this->assertEquals($student->id, $attempt->get_userid());
        $this->assertTrue($attempt->has_response_to_at_least_one_graded_question());
        $this->assertEquals(0, $attempt->get_number_of_unanswered_questions());

        // Check quiz grades.
        $grades = quiz_get_user_grades($quiz, $student->id);
        $grade = array_shift($grades);
        $this->assertEquals(100.0, $grade->rawgrade);

        // Check grade book.
        $gradebookgrades = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $student->id);
        $gradebookitem = array_shift($gradebookgrades->items);
        $gradebookgrade = array_shift($gradebookitem->grades);
        $this->assertEquals(100, $gradebookgrade->grade);
    }

    /**
     * Test create_enrol_teacher
     *
     * @covers ::create_enrol_teacher
     * @return void
     * @throws \dml_exception
     */
    public function test_create_enrol_teacher() {
        $course = $this->getDataGenerator()->create_course();

        $teacher = dummy::create_enrol_teacher($course);
        self::assertNotNull($teacher, "There should be an user object.");

        $context = \context_course::instance($course->id);
        $roles = get_user_roles($context, $teacher->id);
        self::assertIsArray($roles, "Should be the roles array.");
        self::assertIsObject(reset($roles), "Should be a role object.");
        self::assertEquals('editingteacher', reset($roles)->shortname, "Should be editingteacher.");
    }

    /**
     * Test create_enrol_student
     *
     * @covers ::create_enrol_student
     * @return void
     * @throws \dml_exception
     */
    public function test_create_enrol_student() {
        $course = $this->getDataGenerator()->create_course();

        $student = dummy::create_enrol_student($course, 'student@tugraz.at');
        self::assertNotNull($student, "There should be an user object.");

        $context = \context_course::instance($course->id);
        $roles = get_user_roles($context, $student->id);
        self::assertIsArray($roles, "Should be the roles array.");
        self::assertIsObject(reset($roles), "Should be a role object.");
        self::assertEquals('student', reset($roles)->shortname, "Should be student.");
    }

    /**
     * Test set_semester_start_and_end
     *
     * @covers ::set_semester_start_and_end
     * @return void
     * @throws \dml_exception
     */
    public function test_set_semester_start_and_end() {
        $start = new \DateTime('5 months ago');
        $today = new \DateTime('today midnight');
        dummy::set_semester_start_and_end($start, $today);

        self::assertEquals($start->format('Y-m-d'), get_config('local_lytix', 'semester_start'));
        self::assertEquals($today->format('Y-m-d'), get_config('local_lytix', 'semester_end'));
    }

    /**
     * Test add_course_and_set_plattform
     *
     * @covers ::add_course_and_set_plattform
     * @return void
     * @throws \dml_exception
     */
    public function test_add_course_and_set_plattform() {
        $courseid = 123;
        $platform = 'moodle_test';
        dummy::add_course_and_set_plattform($courseid, $platform);

        self::assertEquals($courseid, get_config('local_lytix', 'course_list'));
        self::assertEquals($platform, get_config('local_lytix', 'platform'));
    }

    /**
     * Test assignment functions
     *
     * @covers ::create_assign_instance
     * @covers ::create_assignment
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function test_create_assignment() {
        $start = new \DateTime('5 months ago');
        date_add($start, date_interval_create_from_date_string('6 hours'));
        $today = new \DateTime('today midnight');
        date_add($today, date_interval_create_from_date_string('6 hours'));
        $course = $this->getDataGenerator()->create_course(
            ['enablecompletion' => 1, 'startdate' => $start->getTimestamp(), 'enddate' => $today->getTimestamp()]);

        $instance = dummy::create_assign_instance($course->id, $today->getTimestamp());
        self::assertIsObject($instance, "Should also be an object");
        self::assertNotNull($instance, "Instance cannot be null.");
        self::assertEquals($course->id, $instance->course, "Wrong course id.");

        $assignment = dummy::create_assignment($course, $instance, true);
        self::assertIsObject($assignment, "Should also be an object");
        self::assertNotNull($assignment, "Assigment cannot be null.");
    }

    /**
     * Test complete_activity.
     *
     * @covers ::complete_activity
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function test_complete_activity() {
        // Create data.
        $start = new \DateTime('5 months ago');
        date_add($start, date_interval_create_from_date_string('6 hours'));
        $today = new \DateTime('today midnight');
        date_add($today, date_interval_create_from_date_string('6 hours'));
        $student = $this->getDataGenerator()->create_user(['role' => 'student']);
        $course = $this->getDataGenerator()->create_course(
            ['enablecompletion' => 1, 'startdate' => $start->getTimestamp(), 'enddate' => $today->getTimestamp()]);

        // Create assign instance.
        $instance = dummy::create_assign_instance($course->id);
        // Create an assignment.
        $assign = dummy::create_assignment($course, $instance, true);
        self::assertNotNull($assign);

        // Complete assignment.
        $this->setUser($student);
        dummy::complete_activity($course, 'assign', $instance, $student);

        $completion = new \completion_info($course);
        $result = $completion->get_completions($student->id);
        self::assertNotNull($result);
    }

    /**
     * Test set_aggregation_method
     * @covers ::set_aggregation_method
     * @return void
     * @throws \coding_exception
     */
    public function test_set_aggregation_method() {
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $aggdata     = array(
            'course'       => $course->id,
            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY
        );

        dummy::set_aggregation_method($course, COMPLETION_AGGREGATION_ANY);
        $aggregation = new \completion_aggregation($aggdata);
        self::assertEquals(COMPLETION_AGGREGATION_ANY, $aggregation->method, "Should be 2.");

        dummy::set_aggregation_method($course, COMPLETION_AGGREGATION_ALL);
        $aggregation = new \completion_aggregation($aggdata);
        self::assertEquals(COMPLETION_AGGREGATION_ALL, $aggregation->method, "Should be 1.");
    }
}
