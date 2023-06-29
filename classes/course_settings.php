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
 * @author     Viktoria Wieser
 * @copyright  2021 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper;

/**
 * Class course_settings
 */
class course_settings {

    /**
     * Gets course or semester startdate (Course > Semester)
     * @param int $courseid
     * @return \DateTime
     * @throws \dml_exception
     */
    public static function getcoursestartdate($courseid) {
        $start         = null;
        $course = get_course($courseid);

        if ($course->startdate) {
            $start = (new \DateTime())->setTimestamp($course->startdate);
        }
        $semesterstart = new \DateTime(get_config('local_lytix', 'semester_start'));

        if (($start && $semesterstart->getTimestamp() > $start->getTimestamp()) || !$start) {
            $start = $semesterstart;
        }

        return $start;
    }

    /**
     * Gets course or semester enddate (Course > Semester)
     * @param int $courseid
     * @return \DateTime
     * @throws \dml_exception
     */
    public static function getcourseenddate($courseid) {
        $end         = null;
        $course = get_course($courseid);

        if ($course->enddate) {
            $end = (new \DateTime())->setTimestamp($course->enddate);
        }
        $semesterend = new \DateTime(get_config('local_lytix', 'semester_end'));

        if (($end && $end->getTimestamp() > $semesterend->getTimestamp()) || !$end) {
            $end = $semesterend;
        }

        return $end;
    }

    /**
     * Simple test to figure out, if grade monitor is active in course.
     *
     * @param int $courseid
     * @return bool
     * @throws \dml_exception
     */
    public static function is_grade_monitor_enabled($courseid) {
        return in_array($courseid, explode(',', get_config('local_lytix', 'grade_monitor_list')));
    }

}
