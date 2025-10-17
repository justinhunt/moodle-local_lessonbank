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

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_modcustomfields\customfield\mod_handler;

/**
 * Class fetch_langlevels
 *
 * @package    local_lessonbank
 * @copyright  2025 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch_langlevels extends external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    public static function execute(): array {
        $options = [];

        $modcustomfieldhandler = mod_handler::create();

        foreach($modcustomfieldhandler->get_categories_with_fields() as $categorycontoller) {
            if ($categorycontoller->get('name') === get_string('lessonbankcatname', 'local_lessonbank')) {
                foreach($categorycontoller->get_fields() as $field) {
                    $fieldshortname = $field->get('shortname');
                    if ($fieldshortname === list_minilessons::CUSTOMFIELDS[3] &&
                        $field->get('type') === 'select') {
                        foreach($field->get_options() as $value => $text) {
                            if ($text) {
                                $options[] = [
                                    'value' => $value,
                                    'text' => $text
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $options;
    }

    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'value' => new external_value(PARAM_TEXT, 'option value'),
                'text' => new external_value(PARAM_TEXT, 'option text')
            ])
        );
    }

}
