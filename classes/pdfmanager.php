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
 * Class to manage generated PDF files.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus;

defined('MOODLE_INTERNAL') || die;

/**
 * Class to manage generated PDF files.
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdfmanager {

    /** Prefix of pdf filename **/
    const PREFIX_FILENAME = 'plandecours';

    /**
     * Constructor for pdfmanager.
     *
     * @param context $context
     * @param Syllabus $syllabus
     */
    public function __construct($context, $syllabus) {
        $this->context = $context;
        $this->syllabus = $syllabus;
    }

    /**
     * Generate the PDF file for this syllabus.
     */
    public function generate() {
        global $CFG, $PAGE;

        // Delete the previously generated files for the same syllabus.
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'mod_syllabus', 'generatedpdfs', $this->syllabus->get('id'));

        $doc = new pdfcustom();

        // Set meta-data.
        $doc->SetTitle(get_string('syllabus', 'mod_syllabus').' - '.$this->syllabus->get('title'));
        $doc->SetAuthor(get_string('pdfgeneratedby', 'mod_syllabus'));
        $doc->SetMargins(15, 32);

        // Set the header data (to show on each page).
        $doc->setPrintHeader(true);
        $doc->setHeaderMargin(8);
        $doc->setHeaderData('mod/syllabus/pix/logo_udem.png', 40, get_string('syllabus', 'mod_syllabus'));
        $doc->setImageScale(1.75);
        $vspaces = array();
        $vspaces['h4'] = array(0 => array('h' => 1, 'n' => 3), 1 => array('h' => 0, 'n' => 0));
        $vspaces['p'] = array(0 => array('h' => 1, 'n' => 2), 1 => array('h' => 1, 'n' => 3));
        $doc->setHtmlVSpace($vspaces);

        // Create the first page.
        $doc->AddPage();
        $output = $PAGE->get_renderer('mod_syllabus');
        $page = new \mod_syllabus\output\view_syllabus_page($this->syllabus, $this->context);

        // Syllabus last modified.
        $lastmodified = $output->render_section_pdf($page, 'metadata');
        $fontsizeorigin = $doc->getFontSizePt();
        $doc->SetFont('helvetica', '', 10);
        $doc->Cell(0, 5, $lastmodified, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $doc->SetFontSize($fontsizeorigin);

        // Render all sections.
        $this->addsection($doc, $page, $output, 'generalinformation');
        $this->addsection($doc, $page, $output, 'learningtargeted');
        $this->addsection($doc, $page, $output, 'sessionscalendar');
        $this->addsection($doc, $page, $output, 'evaluations');
        $this->addsection($doc, $page, $output, 'reminders');
        $this->addsection($doc, $page, $output, 'resources');
        $this->addsection($doc, $page, $output, 'regfrmwkinstitpolicies');

        // Save the file permanently on disk.
        $filename = $this->getpdffilename();
        $filepath = make_temp_directory('mod_syllabus/pdfgenerator').'/'.$filename;

        $doc->Output($filepath, 'F');

        $filerecord = new \stdClass();
        $filerecord->contextid = $this->context->id;
        $filerecord->component = 'mod_syllabus';
        $filerecord->filearea = 'generatedpdfs';
        $filerecord->itemid = $this->syllabus->get('id');
        $filerecord->filepath = '/';
        $filerecord->filename = $filename;

        $fs->create_file_from_pathname($filerecord, $filepath);
    }

    /**
     * Return a unique filename for this PDF file.
     *
     * @return string
     */
    private function getpdffilename() {
        global $DB;
        if ($this->syllabus->get('idnumber')) {
            $filenamepart = $this->syllabus->get('idnumber');
        } else {
            $course = $DB->get_record('course', array('id' => $this->syllabus->get('course')), 'fullname', MUST_EXIST);
            $filenamepart = clean_param($course->fullname, PARAM_ALPHANUMEXT);
        }
        $filename = self::PREFIX_FILENAME . '_'.$filenamepart.'_'.date('Y-m-d').'.pdf';
        return $filename;
    }

    /**
     * Return the link to the PDF file for this syllabus.
     *
     * @param stored_file $file Pdf file object.
     * @return string The link to the file, or null if no file generated yet.
     */
    public function getpdflink($file = null) {
        if (!$file) {
            $file = $this->getpdffile();
        }
        if (empty($file)) {
            return null;
        }
        $filename = $file->get_filename();
        $url = \moodle_url::make_pluginfile_url($this->context->id, 'mod_syllabus', 'generatedpdfs', $this->syllabus->get('id'),
            '/', $filename, true);

        return $url;
    }

    /**
     * Return the pdf file for this syllabus.
     *
     * @return stored_file The pdf file object.
     */
    public function getpdffile() {
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_syllabus', 'generatedpdfs', $this->syllabus->get('id'),
            'timemodified DESC', false, 0, 0, 1);
        if (empty($files)) {
            return null;
        }
        return reset($files);
    }

    /**
     * Add a section to the PDF file. Each sections has a bookmark.
     *
     * @param pdfcustom $doc
     * @param view_syllabus_page $page
     * @param renderer $output
     * @param string $sectionname
     */
    private function addsection($doc, $page, $output, $sectionname) {
        $doc->setDestination($sectionname);
        if ($sectionname == 'evaluations') {
            $strsectionname = get_string('assessments', 'mod_syllabus');
        } else {
            $strsectionname = get_string($sectionname, 'mod_syllabus');
        }
        $doc->Bookmark($strsectionname, 0, 0, '', '', array(0, 0, 0), -1, '#'.$sectionname);

        // CSS and HTML should be rendered by the same writeHTML function.
        $htmlcontent = $this->getcss();
        $htmlcontent .= $output->render_section_pdf($page, $sectionname);
        $doc->writeHTML($htmlcontent);
    }

    /**
     * Returns the inline css for the PDF file.
     *
     * @return string
     */
    private function getcss() {
        $html = "<style>
            * {
                font-family: helvetica;
            }
            h4 {
                color: white;
                background-color: #1177d1;
                font-size: 12pt;
                font-weight: bold;
            }
            tr.even td, tr.even th {
                background-color: #f2f2f2;
            }
            p.spacer {
                font-size: 0pt;
            }
            table.greyborder {
                border: 2px solid #ced4da;
            }
            table.greyborder td {
                width: 70%;
                background-color: white;
            }
            table.greyborder td.full {
                width: 100%;
            }
            table.greyborder th {
                width: 30%;
                font-weight: bold;
                background-color: white;
            }
            p.normal {
                font-weight: normal;
            }
            table.backgroundblockinfo {
                border: 2px solid #ced4da;
                background-color: #d6effc;
            }
            table.backgroundblockinfo td {
                width: 75%;
            }
            table.backgroundblockinfo th {
                width: 25%;
                font-weight: normal;
            }
            table.subtable {
                background-color: white;
            }
            table.subtable th {
                width: 30%;
                font-weight: bold;
            }
            table.subtable td {
                width: 70%;
            }
        </style>";
        return $html;
    }
}
