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
 * Custom PDF generation class (extends Moodle pdflib).
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabus;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/pdflib.php');

/**
 * Custom PDF generation class (extends Moodle pdflib).
 *
 * @package    mod_syllabus
 * @copyright  2020 Université de Montréal
 * @author     Marie-Eve Levesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdfcustom extends \pdf {

    /**
     * Redefine the function, to generate a custom header.
     */
    public function Header() {
        $headerdata = $this->getHeaderData();
        // Logo.
        $this->Image(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
        // Title.
        $this->SetFont('helvetica', '', 25);
        $this->SetX(65);
        $this->SetY($this->GetY() + 10);
        $this->Cell(0, 15, $headerdata['title'], 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // Line at the bottom of the header.
        $this->SetY($this->getImageRBY());
        $this->SetX($this->original_lMargin);
        $this->Cell(0, 0, '', 'T', false, 'C');
    }

}
