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
 * Upgrade changes between versions
 *
 * @package   lytix_helper
 * @author    Günther Moser <moser@tugraz.at>
 * @copyright 2021 Educational Technologies, Graz, University of Technology
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or laterB
 */

/**
 * Upgrade Measure Basic DB
 * @param int $oldversion
 * @return bool
 * @throws ddl_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_lytix_helper_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2022032800) {
        // Basic savepoint reached.
        upgrade_plugin_savepoint(true, 2022032800, 'lytix', 'helper');
    }

    if ($oldversion < 2022072500) {
        // Basic savepoint reached.
        upgrade_plugin_savepoint(true, 2022072500, 'lytix', 'helper');
    }

    if ($oldversion < 2022092100) {
        // Basic savepoint reached.
        upgrade_plugin_savepoint(true, 2022092100, 'lytix', 'helper');
    }

    if ($oldversion < 2022092101) {

        // Define index courseid (not unique) to be added to lytix_helper_dly_mdl_acty.
        $table = new xmldb_table('lytix_helper_dly_mdl_acty');
        $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Conditionally launch add index courseid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Helper savepoint reached.
        upgrade_plugin_savepoint(true, 2022092101, 'lytix', 'helper');
    }

    if ($oldversion < 2022102000) {

        // Define index userid (not unique) to be added to lytix_helper_dly_mdl_acty.
        $table = new xmldb_table('lytix_helper_dly_mdl_acty');
        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        // Conditionally launch add index userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Helper savepoint reached.
        upgrade_plugin_savepoint(true, 2022102000, 'lytix', 'helper');
    }

    return true;
}
