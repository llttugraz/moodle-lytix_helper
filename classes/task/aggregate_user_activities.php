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

/**
 * Class aggregate_user_activities
 */
class aggregate_user_activities extends \core\task\scheduled_task
{
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name()
    {
        return get_string('cron_aggregate_user_activities', 'lytix_helper');
    }

    /**
     * Executes Task.
     *
     * @throws \dml_exception
     */
    public function execute()
    {
        global $DB;

        $courseids = explode(',', get_config('local_lytix', 'course_list'));
        $studentroleid = $DB->get_record('role', ['shortname' => 'student'], '*')->id;
        list(, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params = !empty($params) ? 'WHERE e.courseid IN (' . implode(',', $params) . ')' : '';

        $sql = "SELECT DISTINCT ra.userid FROM {role_assignments} ra JOIN {context} c
                      ON ra.contextid = c.id AND c.contextlevel = :contextlevel
                WHERE ra.roleid = :roleid AND ra.userid IN (
                SELECT DISTINCT ue.userid FROM {user_enrolments} ue
                JOIN {enrol} e ON ue.enrolid = e.id
                $params)";

        $myparams['roleid'] = $studentroleid;
        $myparams['contextlevel'] = CONTEXT_COURSE;
        $allstudents = $DB->get_records_sql($sql, $myparams);

        $users = array_keys($allstudents);

        $startdate = new \DateTime(get_config('local_lytix', 'last_aggregation_date'));
        $startdate->setTime(0, 0);

        $today = new \DateTime('today midnight');
        while ($startdate->getTimestamp() < $today->getTimestamp()) {
            $transaction = $DB->start_delegated_transaction();

            $enddate = (new \DateTime())->setTimestamp($startdate->getTimestamp());
            $enddate->modify('+24 hours');

            $aggregated_data = [];

            foreach ($users as $userid) {
                $sql = "SELECT * FROM {logstore_standard_log} logstore WHERE logstore.userid = :userid
                        AND logstore.timecreated >= :startdate AND logstore.timecreated <= :enddate
                        ORDER BY timecreated";

                $params = array();
                $params['userid'] = $userid;
                $params['startdate'] = $startdate->getTimestamp();
                $params['enddate'] = $enddate->getTimestamp();
                $records = $DB->get_records_sql($sql, $params);

                $max_time_gap = 1800; // Equal to 30 minutes.
                $previous_record = null;

                foreach ($records as $record) {
                    $userid = $record->userid;
                    $courseid = $record->courseid;

                    if (!isset($aggregated_data[$userid][$courseid])) {
                        $aggregated_data[$userid][$courseid] = [];
                    }

                    if ($previous_record) {
                        $time_gap = $record->timecreated - $previous_record->timecreated;

                        if ($time_gap >= 0) {
                            if ($this->check_component($record->component)) {
                                $component = $record->component;

                                if (!isset($aggregated_data[$userid][$courseid][$component])) {
                                    $aggregated_data[$userid][$courseid][$component] = ['time' => 0, 'clicks' => 0];
                                }

                                if ($time_gap < $max_time_gap) {
                                    $aggregated_data[$userid][$courseid][$component]['time'] += $time_gap;
                                }
                                $aggregated_data[$userid][$courseid][$component]['clicks']++;
                            }
                        } else {
                            error_log("Negative time gap detected for record ID: {$record->id} 
                            and previous record ID: {$previous_record->id}");
                        }
                    }
                    $previous_record = $record;
                }
            }

            if ($aggregated_data) {

                foreach ($aggregated_data as $userid => $user) {

                    foreach ($user as $courseid => $data) {

                        $record = array();
                        $record['userid'] = $userid;
                        $record['courseid'] = $courseid;
                        foreach ($data as $colname => $colval) {
                            $record[$this->check_component($colname) . '_time'] = $colval['time'];
                            $record[$this->check_component($colname) . '_click'] = $colval['clicks'];
                        }
                        $record['timestamp'] = $startdate->getTimestamp();
                        $record['contextid'] = 1;

                        $DB->insert_record('lytix_helper_dly_mdl_acty', $record);
                    }
                }
            }
            $now = new \DateTime('now');
            set_config('last_aggregation_date', $now->format('Y-m-d'), 'local_lytix');
            $startdate->modify('+24 hours');

            $transaction->allow_commit();
        }
    }

    public function check_component(string $component) {
        $component_map = [
            'core' => 'core',

            'mod_forum' => 'forum',

            'gradereport_overview' => 'grade',

            'mod_url' => 'resource',
            'mod_book' => 'resource',
            'mod_scrom' => 'resource',
            'mod_folder' => 'resource',
            'mod_lession' => 'resource',
            'mod_resource' => 'resource',
            'mod_glossary' => 'resource',
            'mod_lightboxgallery' => 'resource',

            'mod_quiz' => 'quiz',

            'core_h5p' => 'h5p',
            'mod_h5pactivity' => 'h5p',
            'mod_bigbluebuttonbn' => 'bbb',

            'mod_assign' => 'submission',
            'mod_workshop' => 'submission',
            'assignfeedback_file' => 'submission',
            'assignfeedback_editpdf' => 'submission',
            'assignfeedback_comments' => 'submission',
            'assignsubmission_file' => 'submission',
            'assignsubmission_onlinetext' => 'submission',

            'mod_feedback' => 'feedback'
        ];

        return (array_key_exists($component, $component_map)) ? $component_map[$component] : '';
    }
}
