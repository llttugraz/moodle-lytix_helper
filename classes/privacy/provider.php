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
 * @copyright  2022 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper\privacy;
use core\external\exporter;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\tests\request\content_writer;
use core_privacy\local\request\writer;


/**
 * Class provider
 * @package lytix_helper
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @param collection $collection empty collection of tables for column translation
     * @return  collection the translated userdata
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table("lytix_helper_last_aggreg",
            [
                "userid" => "privacy:metadata:lytix_helper_last_aggreg:userid",
                "courseid" => "privacy:metadata:lytix_helper_last_aggreg:courseid",
                "contextid" => "privacy:metadata:lytix_helper_last_aggreg:contextid",
                "timestamp" => "privacy:metadata:lytix_helper_last_aggreg:timestamp",
            ], "privacy:metadata:lytix_helper_last_aggreg"
        );

        $collection->add_database_table("lytix_helper_dly_mdl_acty",
            [
                "userid" => "privacy:metadata:lytix_helper_dly_mdl_acty:userid",
                "courseid" => "privacy:metadata:lytix_helper_dly_mdl_acty:courseid",
                "contextid" => "privacy:metadata:lytix_helper_dly_mdl_acty:contextid",
                "timestamp" => "privacy:metadata:lytix_helper_dly_mdl_acty:timestamp",
                "core_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:core_time",
                "core_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:core_click",
                "forum_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:forum_time",
                "forum_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:forum_click",
                "grade_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:grade_time",
                "grade_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:grade_click",
                "submission_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:submission_time",
                "submission_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:submission_click",
                "resource_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:resource_time",
                "resource_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:resource_click",
                "quiz_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:quiz_time",
                "quiz_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:quiz_click",
                "bbb_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:bbb_time",
                "bbb_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:bbb_click",
                "h5p_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:h5p_time",
                "h5p_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:h5p_click",
                "feedback_time" => "privacy:metadata:lytix_helper_dly_mdl_acty:feedback_time",
                "feedback_click" => "privacy:metadata:lytix_helper_dly_mdl_acty:feedback_click",
            ], "privacy:metadata:lytix_helper_dly_mdl_acty"
        );

        return $collection;
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param \context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel == CONTEXT_USER ||
            $context->contextlevel == CONTEXT_COURSE ||
            $context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records('lytix_helper_last_aggreg');
            $DB->delete_records('lytix_helper_dly_mdl_acty');
        }
    }

    /**
     * Delete all records in lytix_helper for that particular user given by the approved_contextlist
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $DB->delete_records('lytix_helper_last_aggreg', ['userid' => $userid]);
        $DB->delete_records('lytix_helper_dly_mdl_acty', ['userid' => $userid]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        if (empty($userlist->count())) {
            return;
        }
        list(, $userparamsarray) = $DB->get_in_or_equal($userlist);

        $userparamsarray = implode(",", $userparamsarray[0]);

        $DB->delete_records_select('lytix_helper_last_aggreg', "userid IN ({$userparamsarray})");
        $DB->delete_records_select('lytix_helper_dly_mdl_acty', "userid IN ({$userparamsarray})");
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $contextlevels = "SELECT roleid FROM {role_context_levels} WHERE contextlevel = :contextlevel";
        $roleids = "SELECT id FROM {role} WHERE (id IN ({$contextlevels}))";
        $roleassignments = "SELECT userid FROM {role_assignments} WHERE (roleid IN ({$roleids}))";
        $courseids = "SELECT * FROM {lytix_helper_last_aggreg} WHERE (userid IN ({$roleassignments})) AND userid = :userid";

        // This CONTEXT_SYSTEM could be $userlist->contextid.
        $params = [
            "contextlevel" => CONTEXT_COURSE,
            "userid" => $contextlist->get_user()->id,
        ];
        $dataset = $DB->get_records_sql($courseids, $params);

        $contextlist = new contextlist();
        $contextlist->add_system_context();

        writer::with_context($contextlist->get_contexts()[0])
            ->export_data(["lytix_helper_last_aggreg"], (object)$dataset, "Entry of Download");

        // Redo for second table.
        $courseids = "SELECT * FROM {lytix_helper_dly_mdl_acty} WHERE (userid IN ({$roleassignments})) AND userid = :userid";
        $dataset = $DB->get_records_sql($courseids, $params);

        writer::with_context($contextlist->get_contexts()[0])
            ->export_data(["lytix_helper_dly_mdl_acty"], (object)$dataset, "Entry of Download");
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {

        $contextlevels = "SELECT roleid FROM {role_context_levels} WHERE contextlevel = :contextlevel";
        $roleids = "SELECT id FROM {role} WHERE (id IN ({$contextlevels}))";
        $roleassignments = "SELECT contextid FROM {role_assignments} WHERE
                                                (roleid IN ({$roleids})) AND userid = :userid";
        $contextlist = new contextlist();

        $params = [
            "contextlevel" => CONTEXT_SYSTEM,
            "userid" => $userid,
        ];
        $contextlist->add_from_sql($roleassignments, $params);

        $params = [
            "contextlevel" => CONTEXT_COURSE,
            "userid" => $userid,
        ];
        $contextlist->add_from_sql($roleassignments, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $contextlevels = "SELECT roleid FROM {role_context_levels} WHERE contextlevel = :contextlevel";
        $roleids = "SELECT id FROM {role} WHERE (id IN ({$contextlevels}))";
        $roleassignments = "SELECT userid FROM {role_assignments} WHERE (roleid IN ({$roleids}))";
        $courseids = "SELECT userid FROM {lytix_helper_last_aggreg} WHERE (userid IN ({$roleassignments}))";
        $userids = "SELECT * FROM {user} WHERE (id IN ({$courseids}))";

        // This CONTEXT_SYSTEM could be $userlist->contextid.
        $params = [ "contextlevel" => CONTEXT_COURSE ];
        $userlist->add_from_sql("id", $userids, $params);
        $params = [ "contextlevel" => CONTEXT_SYSTEM ];
        $userlist->add_from_sql("id", $userids, $params);

        // Redo for the second table.
        $courseids = "SELECT userid FROM {lytix_helper_dly_mdl_acty} WHERE (userid IN ({$roleassignments}))";
        $userids = "SELECT * FROM {user} WHERE (id IN ({$courseids}))";

        $params = [ "contextlevel" => CONTEXT_COURSE ];
        $userlist->add_from_sql("id", $userids, $params);
        $params = [ "contextlevel" => CONTEXT_SYSTEM ];
        $userlist->add_from_sql("id", $userids, $params);

        return $userlist;
    }
}
