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
 * spreadsheet filter.
 *
 * @package    filter
 * @subpackage spreadsheet
 * @copyright  2006 Dan Stowell
 * @copyright  2007-2008 Szymon Kalasz Internationalisation strings added as part of GHOP
 * @url        http://moodle.org/mod/forum/discuss.php?d=88201
 * @copyright  20011 Geoffrey Rowland <growland at strode-college dot ac dot uk> Updated for Moodle 2
 * @copyright  20013 Geoffrey Rowland <growland at strode-college dot ac dot uk> Updated to use JSmol
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// spreadsheet/JSmol plugin filtering for viewing molecules online
//
// This filter will replace any links to a .mol, .sdf, .csmol, .pdb,
// .pdb.gz .xyz, .cml, .mol2, or .cif file
// with the JavaScript (and associated technologies) needed to display the molecular structure inline
//
// If required, allows customisation of the spreadsheet applet size (default 350 px)
//
// Similarly, allows selection of a few different spreadsheet control sets depending on the chemical context
// e.g. small molecule, biological macromolecule, crystal
//
// Also, customisation of the initial display though spreadsheet scripting
//
// To activate this filter, go to admin and enable 'spreadsheet'.
//
// Latest JSmol version is available from http://chemapps.stolaf.edu/spreadsheet/jsmol.zip
// Unzipped jsmol folder (and contents) can be used to replace/update the jsmol folder in this bundle
// spreadsheet project site: http://spreadsheet.sourceforge.net/
// spreadsheet interactive scripting documentation(Use with spreadsheetSCRIPT{ }): http://chemapps.stolaf.edu/spreadsheet/docs/
// spreadsheet Wiki: http//wiki.spreadsheet.org.

class filter_spreadsheet extends moodle_text_filter {

    public function filter($text, array $options = array()) {
        // Global declared in case YUI JSmol module is inserted elsewhere in page (e.g. JSmol resource artefact?).
        global $CFG, $USER, $yui_jsmol_has_been_configured;
        //$wwwroot = $CFG->wwwroot;
        //$host = preg_replace('~^.*://([^:/]*).*$~', '$1', $wwwroot);

        // Edit $spreadsheetfiletypes to add/remove chemical structure file types that can be displayed.
        // For more detail see: http://wiki.spreadsheet.org/index.php/File_formats.
        //$spreadsheetfiletypes ='cif|cml|csmol|mol|mol2|pdb\.gz|pdb|pse|sdf|xyz';

       $str = '<table>test</table><table class="spreadsheet someOtherClass">content</table>';

       //$newstr = preg_replace('/<table .*?class="(.*?someClass.*?)">(.*?)<\/table>/','JUNNNK',$str);

       //echo $newstr;

       


//        $search = '/<table.*?class="(.*?spreadsheet.*?)">(.*?)<\/table>/';

        $search = '/<span.*?class="(.*?spreadsheet.*?)">(.*?)<\/span>/';
     
        $newtext = preg_replace_callback($search, 'filter_spreadsheet_replace_callback', $text);

//        $newtext = preg_replace($search,'JUNNNK',$text);


        return $newtext;
    }
}

function filter_spreadsheet_replace_callback($matches) {
    global $CFG, $USER;
     //echo $matches[0];
    //echo "In call back";


print_object($matches);

echo $CFG->dirroot;
echo $CFG->wwwroot;

//$script = '<script src="http://localhost/dhtmlxspreadsheet/codebase/spreadsheet.php?math=true&parent=gridbox&sheet='.time().'"></script><div id="gridbox" style="width: 800px; height: 400px; background-color:white;"></div>';
echo $USER->id;
$script = '<script src="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/spreadsheet.php?load=js"></script>';
$script .= '<link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlx_core.css">
<link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlxspreadsheet.css">
<link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/spreadsheet/codebase/dhtmlxgrid_wp.css">';

$script .= '<script>
		window.onload = function() {
			var dhx_sh1 = new dhtmlxSpreadSheet({
				load: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/php/data.php",
				save: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/php/data.php",
				parent: "gridobj1",
				icons_path: "'.$CFG->wwwroot.'/filter/spreadsheet/codebase/imgs/icons/",
				autowidth: true,
				autoheight: false
			}); 

			dhx_sh1.load("1411095319" , "junk");
		};
	</script>
<div class="ssheet_cont" id="gridobj1"></div>';
    return $script;
}
