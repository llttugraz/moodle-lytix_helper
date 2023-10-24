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
 * A helper plugin for LYTIX
 *
 * @package    lytix_helper
 * @author     Güntgher Moser
 * @copyright  2021 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper;

/**
 * Helper class for calculations.
 */
class calculation_helper {
    /**
     * Calculate median.
     * @param array $numbers
     * @return int|mixed
     */
    public static function median($numbers = array()) {
        if (!is_array($numbers) || empty($numbers)) {
            return 0;
        }
        rsort($numbers);
        $count = count($numbers);
        $mid = floor($count / 2);
        return ($count % 2 != 0) ? $numbers[$mid] : (($numbers[$mid - 1] + $numbers[$mid]) / 2);
    }

    /**
     * Calculate mean.
     * @param array $numbers
     * @return float|int
     */
    public static function mean($numbers = array()) {
        $sum = 0.0;
        foreach ($numbers as $number) {
            $sum += $number;
        }
        return count($numbers) != 0 ? ($sum / count($numbers)) : 0.00;
    }

    /**
     * Calculate division.
     * @param int $divident
     * @param int $divisor
     * @return float
     */
    public static function div($divident, $divisor) {

        return $divisor != 0 ? round($divident / $divisor, 2) : 0.00;
    }

    /**
     * Aggregates the time spend for all or just one student in the course. The calculation is done in the DB
     * and the retuned array is can be provided to the plugins timeoverview and activity.
     *
     * @param int $courseid
     * @param int $startdate
     * @param int $enddate
     * @param int $userid
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_activity_aggregation($courseid, $startdate, $enddate, $userid = null) {
        global $DB;

        $sql = "SELECT
                    SUM(logs.core_time) AS core_time_sum,
                    SUM(logs.core_click) AS core_click_sum,
                    SUM(logs.forum_time) AS forum_time_sum,
                    SUM(logs.forum_click) AS forum_click_sum,
                    SUM(logs.grade_time) AS grade_time_sum,
                    SUM(logs.grade_click) AS grade_click_sum,
                    SUM(logs.submission_time) AS submission_time_sum,
                    SUM(logs.submission_click) AS submission_click_sum,
                    SUM(logs.resource_time) AS resource_time_sum,
                    SUM(logs.resource_click) AS resource_click_sum,
                    SUM(logs.quiz_time) AS quiz_time_sum,
                    SUM(logs.quiz_click) AS quiz_click_sum,
                    SUM(logs.bbb_time) AS bbb_time_sum,
                    SUM(logs.bbb_click) AS bbb_click_sum,
                    SUM(logs.h5p_time) AS h5p_time_sum,
                    SUM(logs.h5p_click) AS h5p_click_sum,
                    SUM(logs.feedback_time) AS feedback_time_sum,
                    SUM(logs.feedback_click) AS feedback_click_sum
                FROM {lytix_helper_dly_mdl_acty} logs
                WHERE logs.courseid = :courseid
                    AND logs.timestamp >= :startdate
                    AND logs.timestamp < :enddate";
        if ($userid) {
            $sql .= " AND logs.userid = :userid";
            $params['userid'] = $userid;
        }
        $sql .= ";";

        $params['courseid']  = $courseid;
        $params['startdate'] = $startdate;
        $params['enddate']   = $enddate;

        $record = $DB->get_record_sql($sql, $params);

        $data['time'] = [
            'Aggregation' => get_string('time', 'lytix_helper'),

            'core'       => $record->core_time_sum,
            'forum'      => $record->forum_time_sum,
            'grade'      => $record->grade_time_sum,
            'submission' => $record->submission_time_sum,
            'resource'   => $record->resource_time_sum,
            'quiz'       => $record->quiz_time_sum,
            'video'      => $record->bbb_time_sum + $record->h5p_time_sum,
            'feedback'  => $record->feedback_time_sum,
        ];

        $data['click'] = [
            'Aggregation' => get_string('clicks', 'lytix_helper'),

            'core'       => $record->core_click_sum,
            'forum'      => $record->forum_click_sum,
            'grade'      => $record->grade_click_sum,
            'submission' => $record->submission_click_sum,
            'resource'   => $record->resource_click_sum,
            'quiz'       => $record->quiz_click_sum,
            'video'      => $record->bbb_click_sum + $record->h5p_click_sum,
            'feedback'  => $record->feedback_click_sum,
        ];

        return $data;
    }
}
