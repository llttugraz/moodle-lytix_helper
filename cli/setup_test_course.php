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
 * @author     Günther Moser
 * @copyright  2021 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use lytix_helper\dummy;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../../config.php');

global $CFG, $DB;
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . "/user/lib.php");
require_once($CFG->dirroot . "/user/profile/lib.php");
require($CFG->dirroot . '/course/lib.php');

// Variables to change for your needs.
$numberofstudents = 100;
$coursefullname   = 'Kurs mit ' . $numberofstudents . ' Studierenden';
$courseshortname  = 'kurs_' . $numberofstudents . '_studis';
$coursestartdate  = (new \DateTime('2022-10-01'))->getTimestamp();
$courseenddate    = (new \DateTime('2023-02-28'))->getTimestamp();

// Create users.
if (!$DB->record_exists('user', ['username' => 'testteacher'])) {
    $newuser             = new \stdClass();
    $newuser->username   = 'testteacher';
    $newuser->auth       = 'manual';
    $newuser->confirmed  = true;
    $newuser->mnethostid = $CFG->mnet_localhost_id;
    $newuser->firstname  = 'Test';
    $newuser->lastname   = 'Teacher';
    $newuser->email      = 'test@example.org';
    $newuser->password   = 'Teacher1!';
    $newuser->id         = user_create_user($newuser);
}

$students = [];
for ($i = 0; $i < $numberofstudents; $i++) {
    $username = 'studi_' . $courseshortname . '_' . $i;
    if (!$DB->record_exists('user', ['username' => $username])) {
        $newuser             = new \stdClass();
        $newuser->username   = $username;
        $newuser->auth       = 'manual';
        $newuser->confirmed  = true;
        $newuser->mnethostid = $CFG->mnet_localhost_id;
        $newuser->firstname  = 'Test';
        $newuser->lastname   = 'Student' . $i;
        $newuser->email      = 'test_' . $courseshortname . '_' . $i . '@example.org';
        $newuser->password   = 'Student1!';
        $newuser->id         = user_create_user($newuser);
        $students[]          = $newuser;
    }
}

// Create test course.
if (!$DB->record_exists('course', ['shortname' => $courseshortname])) {
    $course            = new stdClass();
    $course->fullname  = $coursefullname;
    $course->shortname = $courseshortname;
    $course->startdate = $coursestartdate;
    $course->enddate   = $courseenddate;
    // Ignore categories and set all to miscellaneos.
    $course->category  = 1;

    $return = dummy::create_course_and_enrol_users($course, $students);
    $courseid = $return['course']->id;

    // Fill Activity Graph.
    $context = context_course::instance($courseid);
    foreach ($students as $student) {
        if ($student->id) {

            $date = (new \DateTime())->setTimestamp($coursestartdate);
            date_add($date, date_interval_create_from_date_string('2 hours'));
            $today = (new \DateTime())->setTimestamp($courseenddate);
            date_add($today, date_interval_create_from_date_string('2 hours'));

            dummy::create_fake_data_for_course($date, $today, $student, $courseid, $context);
        }
        echo "Data for " . $student->id . " successfully created.\n";
    }
    echo "Course " . $courseid . " with " . $numberofstudents . " students sucessfully created.\n";
}
