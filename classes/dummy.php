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
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . "/user/lib.php");


use advanced_testcase;
use assign;
use coding_exception;
use completion_info;
use context_module;
use grade_item;
use question_engine;
use quiz;
use quiz_attempt;

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
    public static function create_fake_students($limit) {
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
    public static function create_course_and_enrol_users($course, $students) {
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
     * Creates fake activity data for course.
     * @param \DateTime $date
     * @param \DateTime $today
     * @param false|mixed|\stdClass $student
     * @param int $courseid
     * @param false|mixed|\stdClass $context
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function create_fake_data_for_course($date, $today, $student, $courseid, $context) {
        global $DB;
        $logstores = [];

        while ($date->getTimestamp() < $today->getTimestamp()) {
            $logstore            = new \stdClass();
            $logstore->userid    = $student->id;
            $logstore->courseid  = $courseid;
            $logstore->contextid = $context->id;
            $logstore->timestamp = $date->getTimestamp();
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

            date_add($date, date_interval_create_from_date_string('1 day'));
        }
        $DB->insert_records('lytix_helper_dly_mdl_acty', $logstores);
    }

    /**
     * Creates fake planner events for course.
     * @param false|mixed|\stdClass $course
     * @param string $type
     * @param string $marker
     * @param false|mixed|\stdClass $startdate
     * @param false|mixed|\stdClass $enddate
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
    public static function create_fake_planner_event($course, $type, $marker, $startdate, $enddate, $title, $text, $room,
                                                     $visible = 0, $mandatory = 0, $graded = 0, $send = 0) {
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
    public static function complete_fake_planner_event($eventid, $courseid, $userid, $completed, $send, $timestamp) {
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
     * @return stdClass
     * @throws \dml_exception
     */
    public static function create_fake_planner_milestone($course, $user, $type, $marker, $startdate, $enddate,
                                                         $title = 'Title', $text = 'Text...', $offset = 3,
                                                         $option = 'email', $completed = 0, $send = 0) {
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
     * @param false|mixed|\stdClass $record
     * @return bool
     * @throws \dml_exception
     */
    public static function update_fake_planner_milestone($record) {
        global $DB;

        return $DB->update_record('lytix_planner_milestone', $record);
    }

    /**
     * Create a quiz.
     * @param \stdClass|null $course
     * @param int $maxgrade
     * @param int $timeopen
     * @param int $timeclose
     * @return mixed
     * @throws \coding_exception
     */
    public static function create_quiz($course, $maxgrade, $timeopen = 0, $timeclose = 0) {
        // Make a scale and an outcome.
        $scale   = advanced_testcase::getDataGenerator()->create_scale();
        $data    = array('courseid'  => $course->id,
            'fullname'  => 'Quizzes',
            'shortname' => 'Quizzes',
            'scaleid'   => $scale->id);
        $outcome = advanced_testcase::getDataGenerator()->create_grade_outcome($data);

        // Make a quiz with the outcome on.
        $quizgenerator = advanced_testcase::getDataGenerator()->get_plugin_generator('mod_quiz');
        $data          = array('course'                  => $course->id,
            'outcome_' . $outcome->id => 1,
            'grade'                   => $maxgrade,
            'questionsperpage'        => 0,
            'sumgrades'               => 1,
            'completion'              => COMPLETION_TRACKING_MANUAL,
            'completionpass'          => 1,
            'timeopen'                => $timeopen,
            'timeclose'               => $timeclose);
        $quiz          = $quizgenerator->create_instance($data);
        $cm            = get_coursemodule_from_id('quiz', $quiz->cmid);
        return $quiz;
    }

    /**
     * Creates a quiz question.
     * @param \stdClass|null $course
     * @param mixed $quiz
     * @param null|\stdClass $teacher
     * @param int $gradepass
     * @return quiz
     * @throws \coding_exception
     */
    public static function create_quiz_question($course, $quiz, $teacher, $gradepass) {
        // Create a numerical question.
        $questiongenerator = advanced_testcase::getDataGenerator()->get_plugin_generator('core_question');

        $cat      = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        $quizobj = quiz::create($quiz->id, $teacher->id);

        // Set grade to pass.
        $item            = grade_item::fetch(array('courseid'   => $course->id, 'itemtype' => 'mod',
            'itemmodule' => 'quiz', 'iteminstance' => $quiz->id, 'outcomeid' => null));
        $item->gradepass = $gradepass;
        $item->update();

        return $quizobj;
    }

    /**
     * Create a quiz attempt.
     * @param mixed $quizobj
     * @param null|\stdClass $student
     * @param int $timenow
     * @param int $answer
     * @return object|\stdClass
     * @throws \moodle_exception
     */
    public static function create_quiz_attempt($quizobj, $student, $timenow, $answer) {
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $attempt = quiz_create_attempt(
            $quizobj, 1, false, $timenow, false, $student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $tosubmit   = array(1 => array('answer' => $answer));
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);
        return $attempt;
    }

    /**
     * Finish quiz attempt.
     * @param object|\stdClass $attempt
     * @param int $timenow
     */
    public static function finish_quiz_attempt($attempt, $timenow) {
        $attemptobj = quiz_attempt::create($attempt->id);
        advanced_testcase::assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);
    }

    /**
     * Creates and enrols teacher.
     * @param \stdClass|null $course
     * @return \stdClass|null
     * @throws \dml_exception
     */
    public static function create_enrol_teacher($course) {
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
    public static function create_enrol_student($course, $email) {
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
    public static function set_semester_start_and_end($semstart, $semend) {
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
    public static function add_course_and_set_plattform($courseid, $platform) {
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
    public static function create_assign_instance($courseid, $duedate = 0, $allowsubmissionsfromdate = 0) {
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
    public static function complete_activity($course, $modulename, $module, $user) {
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
    public static function set_aggregation_method($course, $method) {
        $aggdata     = array(
            'course'       => $course->id,
            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY
        );
        $aggregation = new \completion_aggregation($aggdata);
        $aggregation->setMethod($method);
        $aggregation->save();
    }
}
