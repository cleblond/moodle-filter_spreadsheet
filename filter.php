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
 * Serve question type files
 *
 * @since      2.0
 * @package    filter
 * @subpackage spreadsheet
 * @copyright  2014 onwards Carl LeBlond 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
class filter_spreadsheet extends moodle_text_filter {
    public function filter($text, array $options = array()) {
        global $CFG, $USER;
        $search = '/<div.*?class="eo_spreadsheet (.*?)".*?<\/div>/';
        $id     = 1;
        $newtext = preg_replace_callback($search, function($matches) use (&$id) {
            global $CFG, $USER, $DB, $PAGE;
            require_once($CFG->dirroot . "/filter/spreadsheet/codebase/php/grid_cell_connector.php");
            $key = '';
            // Must be in database already!
            if ($result = $DB->get_record('filter_spreadsheet_sheet', array('sheetid' => $matches[1]))) {
                if ($result->userid == $USER->id) {
                    $result   = $DB->get_record('filter_spreadsheet_sheet', array(
                        'sheetid' => $matches[1]
                    ));
                    $dbuserid = $result->userid;
                    if ($USER->id == $result->userid) {
                        $key = $result->accesskey;
                    } else if ($result->groupmode == 1) {
                        $context       = $PAGE->context;
                        $coursecontext = $context->get_course_context();
                        $courseid      = $coursecontext->instanceid;
                        $groups        = groups_get_user_groups($courseid, $USER->id);
                        $groupsowner   = groups_get_user_groups($courseid, $result->userid);
                        $intersect     = array_intersect($groups[0], $groupsowner[0]);
                        if (!empty($intersect)) {
                            $key = $result->accesskey;
                            echo "intersect";
                        }
                    } else {
                        $key = '';
                    }
                }
                $indb = true;
            } else { // Not in database..give warning and get out!
                $indb   = false;
                $script = get_string('sheetnotindb', 'filter_spreadsheet');
                return $script;
            }
            $unique = uniqid();
            $script = '<script>
        func' . $id . ' = function() {
            var dhx_sh1' . $id . ' = new dhtmlxSpreadSheet({
                load: "' . $CFG->wwwroot . '/filter/spreadsheet/codebase/php/data.php",
                save: "' . $CFG->wwwroot . '/filter/spreadsheet/codebase/php/data.php",
                parent: "gridobj' . $id . '",
                icons_path: "' . $CFG->wwwroot . '/filter/spreadsheet/codebase/imgs/icons/",
                math: true,
                autowidth: false,
                autoheight: false
            });
            dhx_sh1' . $id . '.load("' . $matches[1] . '" , "' . $key . '");
        };
            </script>
                <div class="ssheet_cont" id="gridobj' . $id . '"></div>';
            $id++;
            return $script;
        }, $text, -1, $count);
        if ($count !== 0) {
            $onload = '<script type="text/javascript">window.onload = function() {';
            for ($i = 1; $i < $id; $i++) {
                $onload .= 'func' . $i . '();';
            }
            $onload .= '}</script>';
            $script = '<script src="' . $CFG->wwwroot . '/filter/spreadsheet/codebase/spreadsheet.php?load=js"></script>';
            $script .= '<link rel="stylesheet" href="' . $CFG->wwwroot . '/filter/spreadsheet/codebase/dhtmlx_core.css">
                       <link rel="stylesheet" href="' . $CFG->wwwroot . '/filter/spreadsheet/codebase/dhtmlxspreadsheet.css">
                       <link rel="stylesheet" href="' . $CFG->wwwroot . '/filter/spreadsheet/codebase/dhtmlxgrid_wp.css">';
            $newtext = $script . $onload . $newtext;
        }
        return $newtext;
    }
}
