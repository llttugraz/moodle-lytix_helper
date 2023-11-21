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
 * This file generates dummy data for the tests.
 *
 * @package   lytix_helper
 * @copyright 2023 Educational Technologies, Graz, University of Technology
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
// Needed for notification_email.php.
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/course/lib.php');
// Needed for the activities generators.
require_once($CFG->dirroot . '/mod/assign/externallib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . "/user/lib.php");


use advanced_testcase;
use assign;
use coding_exception;
use completion_info;
use context_module;
use quiz_attempt;
use quiz;

use question_engine;

/**
 * This class generates dummy data.
 */
class dummy {
    /**
     * Creates fake students
     * @param int $limit
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function create_fake_students(int $limit) {
        global $DB, $CFG;
        $students = [];
        for ($i = 0; $i < $limit; $i++) {
            $username = 'student' . $i;
            if (!$DB->record_exists('user', ['username' => $username])) {
                $newuser             = new \stdClass();
                $newuser->username   = $username;
                $newuser->auth       = 'manual';
                $newuser->confirmed  = true;
                $newuser->mnethostid = $CFG->mnet_localhost_id;
                $newuser->firstname  = 'Test';
                $newuser->lastname   = 'Student' . $i;
                $newuser->email      = 'test' . $i . '@example.org';
                $newuser->password   = 'Student1!';
                $newuser->id         = \user_create_user($newuser);
                $students[]          = $newuser;
            }
        }
        return $students;
    }

    /**
     * Creates fake course and enrols users
     * @param \stdClass $course
     * @param array $students
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function create_course_and_enrol_users(\stdClass $course, array $students) {
        global $DB;
        $newcourse = create_course($course);
        $courseid  = $newcourse->id;

        $enrol    = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', ['enrol' => 'manual', 'courseid' => $courseid]);

        $roleid   = $DB->get_record('role', ['shortname' => 'student'], '*')->id;

        foreach ($students as $student) {
            if ($student->id) {
                $enrol->enrol_user($instance, $student->id, $roleid);
            }
        }
        $return = [
            'course' => $newcourse,
            'student0' => $students[0]
        ];
        return $return;
    }

    /**
     * * Creates fake activity data for course.
     * @param \DateTime $date
     * @param \DateTime $today
     * @param \stdClass $student
     * @param int $courseid
     * @param \stdClass $context
     * @return void
     * @throws \dml_exception
     * @throws coding_exception
     */
    public static function create_fake_data_for_course(\DateTime $date, \DateTime $today, \stdClass $student,
                                                       int $courseid, \stdClass $context) {
        global $DB;
        $logstores = [];

        while ($date->getTimestamp() < $today->getTimestamp()) {
            $logstore            = new \stdClass();
            $logstore->userid    = $student->id;
            $logstore->courseid  = $courseid;
            $logstore->contextid = $context->id;
            $inday = clone $date;
            $inday->setTime(12, 00, 00);
            $logstore->timestamp = $inday->getTimestamp();
            if (!rand(0, 4)) {
                $logstore->core_time       = 0;
                $logstore->forum_time      = 0;
                $logstore->grade_time      = 0;
                $logstore->submission_time = 0;
                $logstore->resource_time   = 0;
                $logstore->quiz_time       = 0;
                $logstore->bbb_time        = 0;
                $logstore->h5p_time        = 0;
                $logstore->feedback_time   = 0;

                $logstore->core_click       = 0;
                $logstore->forum_click      = 0;
                $logstore->grade_click      = 0;
                $logstore->submission_click = 0;
                $logstore->resource_click   = 0;
                $logstore->quiz_click       = 0;
                $logstore->bbb_click        = 0;
                $logstore->h5p_click        = 0;
                $logstore->feedback_click   = 0;

            } else {
                $logstore->core_time       = 10 + rand(0, 60 * 3);
                $logstore->forum_time      = rand(0, 60 * 3);
                $logstore->grade_time      = rand(0, 30);
                $logstore->submission_time = rand(0, 60 * 5);
                $logstore->resource_time   = rand(0, 60 * 20);
                $logstore->quiz_time       = rand(0, 60 * 20);
                $logstore->bbb_time        = (rand(0, 1) ? rand(60 * 30, 60 * 120) : 0);
                $logstore->h5p_time        = (rand(0, 1) ? rand(60 * 15, 60 * 60) : 0);
                $logstore->feedback_time      = rand(0, 60 * 3);

                $logstore->core_click       = (int)ceil($logstore->core_time / 3);;
                $logstore->forum_click      = (int)ceil($logstore->forum_time / 3);
                $logstore->grade_click      = (int)ceil($logstore->grade_time / 10);
                $logstore->submission_click = (int)ceil($logstore->submission_time / 5);
                $logstore->resource_click   = (int)ceil($logstore->resource_time / 20);
                $logstore->quiz_click       = (int)ceil($logstore->quiz_time / 20);
                $logstore->bbb_click        = (int)ceil($logstore->bbb_time / 100);
                $logstore->h5p_click        = (int)ceil($logstore->h5p_time / 100);
                $logstore->feedback_click      = (int)ceil($logstore->forum_time / 3);
            }
            $logstores[] = $logstore;
            $date->modify('+1 day');
        }
        $DB->insert_records('lytix_helper_dly_mdl_acty', $logstores);
    }

    /**
     * Creates fake planner events for course.
     *
     * @param \stdClass $course
     * @param string $type
     * @param string $marker
     * @param int $startdate
     * @param int $enddate
     * @param string $title
     * @param string $text
     * @param string $room
     * @param int $visible
     * @param int $mandatory
     * @param int $graded
     * @param int $send
     * @return \stdClass
     * @throws \dml_exception
     */
    public static function create_fake_planner_event(\stdClass $course, string $type, string $marker, int $startdate,
                                                     int $enddate, string $title, string $text, string $room,
                                                     int $visible = 0, int $mandatory = 0, int $graded = 0, int $send = 0) {
        global $DB;

        $record            = new \stdClass();
        $record->courseid  = $course->id;
        $record->type      = $type;
        $record->marker    = $marker;
        $record->startdate = $startdate;
        $record->enddate   = $enddate;
        $record->title     = $title;
        $record->text      = $text;
        $record->room      = $room;
        $record->visible   = $visible;
        $record->mandatory = $mandatory;
        $record->graded    = $graded;
        $record->send      = $send;

        $record->id = $DB->insert_record('lytix_planner_events', $record);
        return $record;
    }

    /**
     * Completes a fake event.
     * @param int $eventid
     * @param int $courseid
     * @param int $userid
     * @param int $completed
     * @param int $send
     * @param int $timestamp
     * @return \stdClass
     * @throws \dml_exception
     */
    public static function complete_fake_planner_event(int $eventid, int $courseid, int $userid, int $completed,
                                                       int $send, int $timestamp) {
        global $DB;

        $record            = new \stdClass();
        $record->eventid   = $eventid;
        $record->courseid  = $courseid;
        $record->userid    = $userid;
        $record->completed = $completed;
        $record->send      = $send;
        $record->timestamp = $timestamp;

        $record->id = $DB->insert_record('lytix_planner_event_comp', $record);
        return $record;
    }

    /**
     * Creates a fake mlst.
     * @param \stdClass|null $course
     * @param \stdClass|null $user
     * @param string $type
     * @param string $marker
     * @param int $startdate
     * @param int $enddate
     * @param string $title
     * @param string $text
     * @param int $offset
     * @param string $option
     * @param int $completed
     * @param int $send
     * @return \stdClass
     * @throws \dml_exception
     */
    public static function create_fake_planner_milestone(\stdClass $course, \stdClass $user, string $type,
                                                         string $marker, int $startdate, int $enddate,
                                                         string $title = 'Title', string $text = 'Text...',
                                                         int $offset = 3, string $option = 'email',
                                                         int $completed = 0, int $send = 0) {
        global $DB;

        $record            = new \stdClass();
        $record->courseid  = $course->id;
        $record->userid    = $user->id;
        $record->type      = $type;
        $record->marker    = $marker;
        $record->startdate = $startdate;
        $record->enddate   = $enddate;
        $record->title     = $title;
        $record->text      = $text;
        $record->moffset    = $offset;
        $record->moption    = $option;
        $record->completed = $completed;
        $record->send      = $send;

        $record->id = $DB->insert_record('lytix_planner_milestone', $record);
        return $record;
    }

    /**
     * Updates an already created milestone.
     *
     * @param \stdClass $record
     * @return bool
     * @throws \dml_exception
     */
    public static function update_fake_planner_milestone(\stdClass $record) {
        global $DB;

        return $DB->update_record('lytix_planner_milestone', $record);
    }

    /**
     * Create a quiz.
     *
     * @param \stdClass $course
     * @return \stdClass
     * @throws coding_exception
     */
    public static function create_quiz(\stdClass $course) {
        // Make a quiz.
        $quizgenerator = advanced_testcase::getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(['course' => $course->id, 'questionsperpage' => 0, 'grade' => 100.0,
            'sumgrades' => 1]);
        return $quiz;
    }

    /**
     * Creates a quiz question.
     *
     * @param \stdClass $quiz
     * @return \stdClass
     * @throws coding_exception
     */
    public static function create_quiz_question(\stdClass $quiz) {
        // Create a numerical question.
        $questiongenerator = advanced_testcase::getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);

        // Add question to the quiz.
        quiz_add_quiz_question($numq->id, $quiz);

        return $quiz;
    }

    /**
     * Create a quiz attempt.
     * @param null|\stdClass $quiz
     * @param null|\stdClass $student
     * @param int $timenow
     * @return object|\stdClass
     * @throws \moodle_exception
     */
    public static function create_quiz_attempt(\stdClass $quiz, \stdClass $student, int $timenow) {
        $quizobj = quiz::create($quiz->id, $student->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $student->id);

        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        advanced_testcase::assertEquals('1,0', $attempt->layout);

        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        advanced_testcase::assertFalse($attemptobj->has_response_to_at_least_one_graded_question());
        // The student has not answered any questions.
        advanced_testcase::assertEquals(1, $attemptobj->get_number_of_unanswered_questions());

        return $attemptobj;
    }

    /**
     * Finish quiz attempt.
     *
     * @param quiz_attempt|null $attemptobj
     * @param int $timenow
     * @param string $answer
     * @return void
     */
    public static function finish_quiz_attempt(?quiz_attempt $attemptobj, int $timenow, string $answer) {
        $tosubmit = [1 => ['answer' => $answer]];
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // The student has answered the question.
        advanced_testcase::assertEquals(0, $attemptobj->get_number_of_unanswered_questions());
        advanced_testcase::assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);
    }

    /**
     * Creates and enrols teacher.
     * @param \stdClass|null $course
     * @return \stdClass|null
     * @throws \dml_exception
     */
    public static function create_enrol_teacher(\stdClass $course) {
        global $DB;
        $dg = advanced_testcase::getDataGenerator();

        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $teacher     = $dg->create_user();
        if ($dg->enrol_user($teacher->id, $course->id, $teacherrole->id)) {
            return $teacher;
        } else {
            return null;
        }
    }

    /**
     * Creates and enrols student.
     * @param \stdClass|null $course
     * @param string $email
     * @return \stdClass|null
     * @throws \dml_exception
     */
    public static function create_enrol_student(\stdClass $course, string $email) {
        global $DB;
        $dg = advanced_testcase::getDataGenerator();

        $role    = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        $student = $dg->create_user(array('email' => $email));
        if ($dg->enrol_user($student->id, $course->id, $role->id)) {
            return $student;
        } else {
            return null;
        }
    }

    /**
     * Sets semester start and end.
     * @param \DateTime $semstart
     * @param \DateTime $semend
     */
    public static function set_semester_start_and_end(\DateTime $semstart, \DateTime $semend) {
        $semstart->setTime(0, 0);
        set_config('semester_start', $semstart->format('Y-m-d'), 'local_lytix');

        $semend->setTime(0, 0);
        set_config('semester_end', $semend->format('Y-m-d'), 'local_lytix');
    }

    /**
     * Adds course to config list and sets plattform.
     * @param int $courseid
     * @param string $platform
     */
    public static function add_course_and_set_plattform(int $courseid, string $platform) {
        // Add course to config list.
        set_config('course_list', $courseid, 'local_lytix');
        // Set platform.
        set_config('platform', $platform, 'local_lytix');
    }

    /**
     * Create assign instance.
     * @param int $courseid
     * @param int $duedate
     * @param int $allowsubmissionsfromdate
     * @return mixed
     * @throws \coding_exception
     */
    public static function create_assign_instance(int $courseid, int $duedate = 0, int $allowsubmissionsfromdate = 0) {
        $dg = advanced_testcase::getDataGenerator();

        $generator                                 = $dg->get_plugin_generator('mod_assign');
        $params['course']                          = $courseid;
        $params['assignfeedback_file_enabled']     = 1;
        $params['assignfeedback_comments_enabled'] = 1;
        $params['duedate']                         = $duedate;
        $params['completion']                      = 2;
        $params['completionpass']                  = 1;
        $params['allowsubmissionsfromdate']        = $allowsubmissionsfromdate;
        return $generator->create_instance($params);
    }

    /**
     * Create assignment
     * @param \stdClass $course
     * @param \stdClass $instance
     * @param bool $advanced
     * @return assign
     * @throws \dml_exception
     * @throws coding_exception
     */
    public static function create_assignment(\stdClass $course, \stdClass $instance, bool $advanced = false): assign {
        global $DB;
        $cm       = get_coursemodule_from_instance('assign', $instance->id);
        $context  = context_module::instance($cm->id);
        if ($advanced) {
            $moduleid = $DB->get_field('modules', 'id', ['name' => 'assign']);
            $cmid = $DB->get_field('course_modules', 'id',
                [
                    'module' => $moduleid,
                    'instance' => $instance->id,
                    'course' => $course->id
                ]);
            if ($cmid) {
                $DB->set_field('course_modules', 'completion', '2', ['id' => $cmid]);
            }
            if (!$DB->record_exists('course_completion_criteria',
                ['course' => $course->id, 'criteriatype' => 4, 'module' => 'assign',
                    'moduleinstance' => $cmid])) {
                $completion = new \stdClass();
                $completion->course = $course->id;
                $completion->criteriatype = 4;
                $completion->module = 'assign';
                $completion->moduleinstance = $cmid;
                $DB->insert_record('course_completion_criteria', $completion);
            }
        }
        return new assign($context, $cm, $course);
    }

    /**
     * Complete activity.
     *
     * @param \stdClass|null $course
     * @param string $modulename
     * @param mixed $module
     * @param null|\stdClass $user
     */
    public static function complete_activity(\stdClass $course, string $modulename, $module, \stdClass $user) {
        $cm         = get_coursemodule_from_id($modulename, $module->cmid);
        $completion = new completion_info($course);
        $completion->update_state($cm, COMPLETION_COMPLETE, $user->id);
    }

    /**
     * Set aggregation method.
     *
     * @param \stdClass|null $course
     * @param int $method
     * @throws coding_exception
     */
    public static function set_aggregation_method(\stdClass $course, int $method) {
        $aggdata     = array(
            'course'       => $course->id,
            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY
        );
        $aggregation = new \completion_aggregation($aggdata);
        $aggregation->setMethod($method);
        $aggregation->save();
    }
}
