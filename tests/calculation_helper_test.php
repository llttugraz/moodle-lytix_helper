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
 * @author     Günther Moser <moser@tugraz.at>
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_helper;


/**
 * Class privacy_lib_test
 * @coversDefaultClass \lytix_helper\calculation_helper
 */
class calculation_helper_test extends \advanced_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    public function generateData(): void {
        global $DB;

        $records = [
            (object)[
                'userid' => 1,
                'courseid' => 101,
                'contextid' => 1,
                'timestamp' => strtotime('2023-10-01'),
                'core_time' => 10,
                'forum_time' => 20,
                // ... andere Felder
            ],
            // ... weitere Datensätze
        ];

        // Einfügen der Testdatensätze in die Datenbank
        foreach ($records as $record) {
            $DB->insert_record('lytix_helper_dly_mdl_acty', $record);
        }
    }

    /**
     * Testing the helper median function.
     * @covers ::median
     * @return void
     */
    public function test_median() {
        // Test case: Empty array.
        $numbers = array();
        $expected = 0;
        $actual = calculation_helper::median($numbers);
        $this->assertEquals($expected, $actual);

        // Test case: Odd number of elements.
        $numbers = array(1, 3, 5, 7, 9);
        $expected = 5;
        $actual = calculation_helper::median($numbers);
        $this->assertEquals($expected, $actual);

        // Test case: Even number of elements.
        $numbers = array(2, 4, 6, 8);
        $expected = 5;
        $actual = calculation_helper::median($numbers);
        $this->assertEquals($expected, $actual);

        // Test case: Non-numeric elements.
        $numbers = array('a', 'b', 'c');
        $expected = 'b';
        $actual = calculation_helper::median($numbers);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Testing the helper mean function.
     * @covers ::mean
     * @return void
     */
    public function test_mean() {
        // Test case: Empty array.
        $numbers = array();
        $expected = 0.00;
        $actual = calculation_helper::mean($numbers);
        $this->assertEquals($expected, $actual);

        // Test case: Array with positive numbers.
        $numbers = array(1, 2, 3, 4, 5);
        $expected = 3.00;
        $actual = calculation_helper::mean($numbers);
        $this->assertEquals($expected, $actual);

        // Test case: Array with negative numbers.
        $numbers = array(-1, -2, -3, -4, -5);
        $expected = -3.00;
        $actual = calculation_helper::mean($numbers);
        $this->assertEquals($expected, $actual);

        // Test case: Array with mixed positive and negative numbers.
        $numbers = array(-2, 4, -6, 8, -10);
        $expected = -1.20;
        $actual = calculation_helper::mean($numbers);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Testing the helper div function.
     * @covers ::div
     * @return void
     */
    public function test_div() {
        // Test case: Divisor is not zero.
        $divident = 10;
        $divisor = 2;
        $expected = 5.00;
        $actual = calculation_helper::div($divident, $divisor);
        $this->assertEquals($expected, $actual);

        // Test case: Divisor is zero.
        $divident = 10;
        $divisor = 0;
        $expected = 0.00;
        $actual = calculation_helper::div($divident, $divisor);
        $this->assertEquals($expected, $actual);

        // Test case: Divident is zero.
        $divident = 0;
        $divisor = 5;
        $expected = 0.00;
        $actual = calculation_helper::div($divident, $divisor);
        $this->assertEquals($expected, $actual);

        // Test case: Divident and divisor are negative.
        $divident = -10;
        $divisor = -2;
        $expected = 5.00;
        $actual = calculation_helper::div($divident, $divisor);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Testing the aggregation of times in our table.
     * @return void
     */
    public function test_get_activity_aggregation() {
        self::assertTrue(false);


                // Aufruf der Funktion
                $result = your_class_name::get_activity_aggregation(101, strtotime('2023-10-01'), strtotime('2023-10-02'));

                // Überprüfen des Ergebnisses
                $this->assertEquals(10, $result['time']['core']);
                $this->assertEquals(20, $result['time']['forum']);
                // ... weitere Assertions
            }


}
