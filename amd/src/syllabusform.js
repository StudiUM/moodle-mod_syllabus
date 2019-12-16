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
 * JavaScript for the syllabus form class.
 *
 * @module    mod_syllabus/syllabusform
 * @package   mod_syllabus
 * @copyright 2019 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/notification'], function($, str, notification) {

    return  {
        init: function() {

            str.get_strings([
                { key: 'collapseall'},
                { key: 'expandall'}]
            ).done(
                function (strings) {
                    var collapseall = strings[0];
                    var expandall = strings[1];
                    // Collapse/Expand all.
                    $(".syllabus").on('click', '.collapsible-actions a', function(event) {
                        event.preventDefault();
                        var tabid = $(this).closest('div.tab-pane').attr('id');
                        if ($(this).hasClass('collapse-all')) {
                            $(this).text(expandall);
                            $(this).toggleClass("collapse-all expand-all");
                            $('.syllabus #' + tabid + ' fieldset.collapsible:not(.collapsed) a.fheader').trigger('click');
                        } else {
                            $(this).text(collapseall);
                            $(this).toggleClass("collapse-all expand-all");
                            $('.syllabus #' + tabid + ' fieldset.collapsible.collapsed a.fheader').trigger('click');
                        }
                    });

                    $(".syllabus").on('click', 'ul.nav-tabs li.nav-item a', function(event) {
                        event.preventDefault();
                        var tabid = $(this).data('tab');
                        $('input[name="rubric"]').val(tabid);
                    });

                    // Single collapse/expand.
                    $('.syllabus legend a.fheader').on('click', function(event) {
                        event.preventDefault();
                        var id_fieldset = $(this).attr('aria-controls');
                        var expanded = $(this).attr('aria-expanded');
                        var tabid = $(this).closest('div.tab-pane').attr('id');
                        if (expanded === true) {
                            $(this).attr('aria-expanded', false);
                        } else {
                            $(this).attr('aria-expanded', true);
                        }
                        $('#' + id_fieldset).toggleClass('collapsed');
                        var allcollapsed = true;
                        var allexpanded = true;
                        $('.syllabus #' + tabid + ' fieldset.collapsible').each(function() {
                            allcollapsed = $(this).hasClass('collapsed') && allcollapsed;
                            allexpanded = !$(this).hasClass('collapsed') && allexpanded;
                        });
                        var linkcollapseallexpandall = $('.syllabus #' + tabid + ' .collapsible-actions a');
                        if (linkcollapseallexpandall.hasClass('collapse-all') &&
                                allcollapsed) {
                            linkcollapseallexpandall.text(expandall);
                            linkcollapseallexpandall.toggleClass("collapse-all expand-all");
                        } else if (linkcollapseallexpandall.hasClass('expand-all') &&
                                allexpanded) {
                            linkcollapseallexpandall.text(collapseall);
                            linkcollapseallexpandall.toggleClass("collapse-all expand-all");
                        }
                    });
                }
            ).fail(notification.exception);
        }
    };
});
