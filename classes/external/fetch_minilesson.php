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

namespace local_lessonbank\external;

use core\context\module;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_api;
use core_external\external_value;
use mod_minilesson\import;

/**
 * Implementation of web service local_lessonbank_fetch_minilesson
 *
 * @package    local_lessonbank
 * @copyright  2025 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch_minilesson extends external_api {

    /**
     * Describes the parameters for local_lessonbank_fetch_minilesson
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Minilesson id'),
        ]);
    }

    /**
     * Implementation of web service local_lessonbank_fetch_minilesson
     *
     * @param mixed $id
     */
    public static function execute($id) {
        global $DB;
        // Parameter validation.
        ['id' => $id] = self::validate_parameters(
            self::execute_parameters(),
            ['id' => $id]
        );

        $moduleinstance = $DB->get_record('minilesson', ['id' => $id], '*', MUST_EXIST);
        [$course, $cm] = get_course_and_cm_from_instance($moduleinstance->id, 'minilesson');
        $context = module::instance($cm->id);

        $theimport = new import($moduleinstance, $context, $course, $cm);
        $response['json'] = $theimport->export_items();

        return $response;
    }

    /**
     * Describe the return structure for local_lessonbank_fetch_minilesson
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'json' => new external_value(PARAM_RAW, 'json of file')
        ]);
    }
}
