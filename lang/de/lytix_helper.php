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
 * @author     Alexander Kremser
 * @copyright  2021 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Lytix Helper';

$string['privacy:metadata'] = 'This plugin does not store any data.';

$string['time'] = 'Zeit';
$string['clicks'] = 'Klicks';

// Tasks.
$string['cron_aggregate_user_activities'] = "Aggregate user activities for lytix subplugin activity basic";

// Errors & Warnings.
$string['generic_error'] = 'Etwas ist schiefgelaufen. Bitte laden Sie die Seite erneut oder kontaktieren Sie den Support, falls der Fehler erneut auftritt.';
$string['no_data_available'] = 'Noch sind nicht genug Daten verfügbar.';
$string['fetch_failed'] = 'Die benötigten Daten konnten nicht geladen werden. Laden Sie die Seite erneut, um es noch einmal zu versuchen.';
$string['template_render_error'] = 'Ein interner Fehler trat auf. Bitte melden Sie den Vorfall dem Support-Team.';

// Privacy.
$string['privacy:metadata:lytix_helper_last_aggreg'] = "Um das Verhalten von Personen im Kurs zu überwachen, müssen einige Benutzerdaten gespeichert werden";
$string['privacy:metadata:lytix_helper_last_aggreg:userid'] = "Die Benutzernummer wird gespeichert, um die Person, die den Kurs besucht hat, identifizieren zu können";
$string['privacy:metadata:lytix_helper_last_aggreg:courseid'] = "Die Kursnummer wird gespeichert, um nachvollziehen zu können, von welchem Kurs die Daten erhoben wurden";
$string['privacy:metadata:lytix_helper_last_aggreg:contextid'] = "Contextid";
$string['privacy:metadata:lytix_helper_last_aggreg:timestamp'] = "zeitstempel";
$string['privacy:metadata:lytix_helper_dly_mdl_acty'] = "Um das Verhalten von Personen im Kurs zu überwachen, müssen einige Benutzerdaten gespeichert werden";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:userid'] = "Die Benutzernummer wird gespeichert, um die Person, die den Kurs besucht hat, identifizieren zu können";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:courseid'] = "Die Kursnummer wird gespeichert, um nachvollziehen zu können, von welchem Kurs die Daten erhoben wurden";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:contextid'] = "Contextid";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:timestamp'] = "zeitstempel";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:core_time'] = "Zeit im Kurs";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:core_click'] = "Klicks im Kurs";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:forum_time'] = "Zeit im Forum";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:forum_click'] = "Klicks im Forum";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:grade_time'] = "Zeit im Gradebook";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:grade_click'] = "Klicks im Gradebook";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:submission_time'] = "Zeit in Abgaben";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:submission_click'] = "Klicks in Abgaben";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:resource_time'] = "Zeit in Ressourcen";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:resource_click'] = "Klicks in Ressourcen";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:quiz_time'] = "Zeit in Quizzen";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:quiz_click'] = "Klicks in Quizzen";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:bbb_time'] = "Zeit in BBB";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:bbb_click'] = "Klicks in BBB";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:h5p_time'] = "Zeit in h5p";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:h5p_click'] = "Klicks in h5p";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:feedback_time'] = "Zeit im Feedback";
$string['privacy:metadata:lytix_helper_dly_mdl_acty:feedback_click'] = "Klicks im Feedback";
