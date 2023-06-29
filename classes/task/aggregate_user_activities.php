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
 * Learners Corner click activities will be aggregated and saved in database
 *
 * @package    lytix_helper
 * @category   task
 * @author     Günther Moser <moser@tugraz.at>
 * @author     Viktoria Wieser <viktoria.wieser@tugraz.at>
 * @copyright  2021 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper\task;

use context_course;
use lytix_helper\course_settings;
use lytix_helper\types;

/**
 * Class aggregate_user_activities
 */
class aggregate_user_activities extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('cron_aggregate_user_activities', 'lytix_helper');
    }

    /**
     * Executes Task.
     *
     * @throws \dml_exception
     */
    public function execute() {
        global $DB;
        // Get last execution and start with aggregation per day.
        $courseids = explode(',', get_config('local_lytix', 'course_list'));
        foreach ($courseids as $courseid) {
            try {
                $course = get_course($courseid);
            } catch (\dml_exception $e) {
                echo $e->getMessage();
            }
            if (!$courseid || is_null($course)) {
                continue;
            }
            $context       = context_course::instance($courseid);
            $studentroleid = $DB->get_record('role', ['shortname' => 'student'], '*')->id;
            $students      = get_role_users($studentroleid, $context);

            foreach ($students as $user) {
                // Get startdate for calculation.
                if (!$DB->record_exists('lytix_helper_last_aggreg',
                                        ['userid' => $user->id, 'courseid' => $courseid, 'contextid' => $context->id])) {

                    $startdate     = course_settings::getcoursestartdate($courseid);
                    $startdate->setTime(0, 0);
                    date_add($startdate, date_interval_create_from_date_string('2 hours'));
                } else {
                    $timestamp = $DB->get_record('lytix_helper_last_aggreg',
                                                 ['userid' => $user->id, 'courseid' => $courseid, 'contextid' => $context->id]);
                    $startdate = (new \DateTime())->setTimestamp($timestamp->timestamp);
                }

                $end = course_settings::getcourseenddate($courseid);
                $today       = new \DateTime('today midnight');
                date_add($today, date_interval_create_from_date_string('2 hours'));

                while ($startdate->getTimestamp() < $today->getTimestamp() &&
                       $today->getTimestamp() < $end->getTimestamp()) {
                    $enddate = (new \DateTime())->setTimestamp($startdate->getTimestamp());
                    date_add($enddate, date_interval_create_from_date_string('1 day'));

                    $this->aggregate_all_events_per_day($user->id, $courseid, $context->id, $startdate, $enddate);
                    date_add($startdate, date_interval_create_from_date_string('1 day'));
                    $this->set_new_timestamp($user->id, $courseid, $context->id, $enddate->getTimestamp());
                }
            }
        }
    }

    /**
     * Sets timestamp for last log task.
     * @param int $userid
     * @param int $courseid
     * @param int $contextid
     * @param int $timestamp
     * @return bool|int
     * @throws \dml_exception
     */
    public function set_new_timestamp($userid, $courseid, $contextid, $timestamp) {
        global $DB;

        if ($DB->record_exists('lytix_helper_last_aggreg',
                               ['userid' => $userid, 'courseid' => $courseid, 'contextid' => $contextid])) {
            $record            = $DB->get_record('lytix_helper_last_aggreg',
                                                 ['userid' => $userid, 'courseid' => $courseid, 'contextid' => $contextid]);
            $record->timestamp = $timestamp;
            return $DB->update_record('lytix_helper_last_aggreg', $record);
        } else {
            $record            = new \stdClass();
            $record->userid    = $userid;
            $record->courseid  = $courseid;
            $record->contextid = $contextid;
            $record->timestamp = $timestamp;
            return $DB->insert_record('lytix_helper_last_aggreg', $record);
        }
    }

    /**
     * Aggregate moodle events per day.
     * @param int $userid
     * @param int $courseid
     * @param int $contextid
     * @param int $startdate
     * @param int $enddate
     */
    public function aggregate_moodle_events_per_day($userid, $courseid, $contextid, $startdate, $enddate) {
        global $DB;

        $sql = "SELECT *
            FROM {logstore_standard_log} logstore
            WHERE logstore.userid = :userid AND logstore.courseid = :courseid
            AND logstore.timecreated >= :startdate AND logstore.timecreated <= :enddate
            ORDER BY timecreated ASC";

        $params['userid']    = $userid;
        $params['courseid']  = $courseid;
        $params['contextid'] = $contextid;
        $params['startdate'] = $startdate;
        $params['enddate']   = $enddate;

        $records = $DB->get_records_sql($sql, $params);
        $this->calcualte_moodle_event_duration($userid, $courseid, $contextid, $startdate, $records);

    }

    /**
     * Calculate the moodle event duration.
     * @param int $userid
     * @param int $courseid
     * @param int $contextid
     * @param int $day
     * @param array $records
     */
    public function calcualte_moodle_event_duration($userid, $courseid, $contextid, $day, $records) {

        global $DB;

        // General info.
        $user            = new \stdClass();
        $user->userid    = $userid;
        $user->courseid  = $courseid;
        $user->contextid = $contextid;
        $user->timestamp = $day;

        // Aggregate times.
        $user->core_time       = 0;
        $user->forum_time      = 0;
        $user->grade_time      = 0;
        $user->submission_time = 0;
        $user->resource_time   = 0;
        $user->quiz_time       = 0;
        $user->bbb_time        = 0;
        $user->h5p_time        = 0;
        $user->feedback_time   = 0;

        // Aggregate clicks.
        $user->core_click       = 0;
        $user->forum_click      = 0;
        $user->grade_click      = 0;
        $user->submission_click = 0;
        $user->resource_click   = 0;
        $user->quiz_click       = 0;
        $user->bbb_click        = 0;
        $user->h5p_click        = 0;
        $user->feedback_click   = 0;

        $records = array_values($records);
        foreach ($records as $i => $record) {
            if ($record->courseid == $courseid) {
                switch ($record->component) {
                    case 'core':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_core_time($user, $record, $next);
                        break;
                    case 'mod_forum':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_forum_time($user, $record, $next);
                        break;
                    case 'gradereport_overview':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_grade_time($user, $record, $next);
                        break;
                    case 'mod_url':
                    case 'mod_book':
                    case 'mod_scrom':
                    case 'mod_folder':
                    case 'mod_lession':
                    case 'mod_resource':
                    case 'mod_glossary':
                    case 'mod_lightboxgallery':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_resource_time($user, $record, $next);
                        break;
                    case 'mod_quiz':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_quiz_time($user, $record, $next);
                        break;
                    case 'mod_bigbluebuttonbn':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_bbb_time($user, $record, $next);
                        break;
                    case 'core_h5p':
                    case 'mod_h5pactivity':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_h5p_time($user, $record, $next);
                        break;
                    case 'mod_assign':
                    case 'mod_workshop':
                    case 'assignfeedback_file':
                    case 'assignfeedback_editpdf':
                    case 'assignfeedback_comments':
                    case 'assignsubmission_file':
                    case 'assignsubmission_onlinetext':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_submission_time($user, $record, $next);
                        break;
                    case 'mod_feedback':
                        $next = array_key_exists(($i + 1), $records) ? $records[$i + 1] : false;
                        $this->add_feedback_time($user, $record, $next);
                        break;
                    default:
                        break;
                }
            }
        }

        try {
            $DB->insert_record('lytix_helper_dly_mdl_acty', $user);
        } catch (\dml_exception $e) {
            echo $e;
        }
    }

    /**
     * Adds core time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass|boolean $next
     */
    private function add_core_time(&$user, $record, $next) {
        $user->core_time += $this->add_time($record, $next);
        $user->core_click += 1;
    }

    /**
     * Adds forum time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_forum_time(&$user, $record, $next) {
        $user->forum_time += $this->add_time($record, $next);
        $user->forum_click += 1;
    }

    /**
     * Adds grade time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_grade_time(&$user, $record, $next) {
        $user->grade_time += $this->add_time($record, $next);
        $user->grade_click += 1;
    }

    /**
     * Adds resource time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_resource_time(&$user, $record, $next) {
        $user->resource_time += $this->add_time($record, $next);
        $user->resource_click += 1;
    }

    /**
     * Adds quiz time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_quiz_time(&$user, $record, $next) {
        $user->quiz_time += $this->add_time($record, $next);
        $user->quiz_click += 1;
    }

    /**
     * Adds bbb time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_bbb_time(&$user, $record, $next) {
        $user->bbb_time += $this->add_time($record, $next);
        $user->bbb_click += 1;
    }

    /**
     * Adds h5p time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_h5p_time(&$user, $record, $next) {
        $user->h5p_time += $this->add_time($record, $next);
        $user->h5p_click += 1;
    }

    /**
     * Adds submission time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_submission_time(&$user, $record, $next) {
        $user->submission_time += $this->add_time($record, $next);
        $user->submission_click += 1;
    }

    /**
     * Adds feedback time for this user.
     * @param \stdClass $user
     * @param \stdClass $record
     * @param \stdClass $next
     */
    private function add_feedback_time(&$user, $record, $next) {
        $user->feedback_time += $this->add_time($record, $next);
        $user->feedback_click += 1;
    }

    /**
     * Add the time for this event.
     * @param false|mixed|\stdClass $record
     * @param false|mixed|\stdClass $next
     * @return int
     */
    public function add_time($record, $next) {
        if (!$next) {
            return 10; // Std ten seconds.
        }

        $first    = (new \DateTime())->setTimestamp($record->timecreated);
        $second   = (new \DateTime())->setTimestamp($next->timecreated);
        $interval = $second->diff($first);

        if ((int) $interval->format('h') > 1 && $next->eventname != 'mod_bigbluebuttonbn') {
            return 10; // Std ten seconds.
        }

        return ($second->getTimestamp() - $first->getTimestamp()) + 1;
    }

    /**
     * Aggregate all events per this day.
     * @param int $userid
     * @param int $courseid
     * @param int $contextid
     * @param false|mixed|\stdClass $startdate
     * @param false|mixed|\stdClass $enddate
     * @throws \dml_exception
     */
    public function aggregate_all_events_per_day($userid, $courseid, $contextid, $startdate, $enddate) {
        $this->aggregate_moodle_events_per_day($userid, $courseid, $contextid, $startdate->getTimestamp(),
                                               $enddate->getTimestamp());
    }
}
