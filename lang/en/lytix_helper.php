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
 * Helper plugin for lytix
 *
 * @package    lytix_helper
 * @author     Viktoria Wieser
 * @copyright  2020 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Lytix Helper';

$string['privacy:metadata'] = 'This plugin does not store any data.';

$string['time'] = 'Time';
$string['clicks'] = 'Clicks';

// Tasks.
$string['cron_aggregate_user_activities'] = "Aggregate user activities for Lytix";

// Errors & Warnings.
$string['generic_error'] = 'Something went wrong. Please try reloading the page or report this incident if the error persists.';
$string['no_data_available'] = 'There is not enough data available yet.';
$string['fetch_failed'] = 'The required data could not be fetched. Reload the page to try again.';
$string['template_render_error'] = 'There was an internal error. Please contact the support team about this.';

// Privacy.
$string['privacy:metadata:lytix_helper_last_aggreg'] = "In order to track all activities of the users, we need to save some user related data";
$string['privacy:metadata:lytix_helper_last_aggreg:userid'] = "The user ID will be saved for uniquely identifying the user";
$string['privacy:metadata:lytix_helper_last_aggreg:courseid'] = "The course ID will be saved for knowing to which course
 the data belongs to";
$string['privacy:metadata:lytix_helper_last_aggreg:contextid'] = "Contextid";
$string['privacy:metadata:lytix_helper_last_aggreg:timestamp'] = "Timestamp";
$string['privacy:metadata:lytix_helper_dly_mdl_acty'] = "In order to track all activities of the users, we need to save some user related data";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:userid'] = "The user ID will be saved for uniquely identifying the user";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:courseid'] = "The course ID will be saved for knowing to which course
 the data belongs to";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:contextid'] = "Contextid";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:timestamp'] = "Timestamp";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:core_time'] = "Time in Course";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:core_click'] = "Clicks in Course";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:forum_time'] = "Time in Forum";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:forum_click'] = "Clicks in Forum";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:grade_time'] = "Time in Gradebook";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:grade_click'] = "Clicks in Gradebook";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:submission_time'] = "Time in Submission";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:submission_click'] = "Clicks in Submission";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:resource_time'] = "Time in Resource";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:resource_click'] = "Clicks in Resource";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:quiz_time'] = "Time in Quiz";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:quiz_click'] = "Clicks in Quiz";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:bbb_time'] = "Time in BBB";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:bbb_click'] = "Click in BBB";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:h5p_time'] = "Time in h5p";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:h5p_click'] = "Clicks in h5p";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:feedback_time'] = "Time in Feedback";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:feedback_click'] = "Clicks in Feedback";
