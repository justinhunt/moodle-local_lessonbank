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
 * English language pack for Lessonbank
 *
 * @package    local_lessonbank
 * @category   string
 * @copyright  2025 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['descriptioncolon'] = 'Description:';
$string['embedcookie'] = 'Allow lesson previews in an iframe';
$string['embedcookie_desc'] = 'Re-issue the session cookie as SameSite=None; Secure; Partitioned when a page is requested from another site, so that a lesson previewed in an iframe on a remote Moodle keeps its session instead of being redirected to the login page. The cookie is partitioned by the embedding site, so each preview gets its own guest session and a first party session here is never shared cross site. Safari blocks third party cookies, so previews may still not work there.';
$string['foundlessons'] = 'Found {$a} lessons';
$string['items'] = 'Items';
$string['itemscolon'] = 'Items:';
$string['keywordcolon'] = 'Keyword:';
$string['keywords'] = 'Keywords';
$string['lessonbank'] = 'Lesson bank';
$string['lessonbankcatname'] = 'Lesson bank';
$string['level'] = 'Level';
$string['nolessonitemfound'] = 'No item found';
$string['perpage'] = 'Per page';
$string['pluginname'] = 'Lessonbank';
$string['showadvanced'] = 'Show advanced';
$string['targetlang'] = 'Target Lang';
$string['title'] = 'Title';
