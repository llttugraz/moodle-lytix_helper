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
}
