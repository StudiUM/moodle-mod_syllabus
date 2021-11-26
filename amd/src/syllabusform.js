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
 * @copyright 2019 Université de Montréal
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/notification', 'mod_syllabus/requiredfields_popup'],
    function ($, str, notification, requiredfieldspopup) {

        /**
         * Syllabus form.
         * @param {Array} configdatepicker
         * @param {Array} configmanageline
         * @param {string} fieldfortypeofsubmit
         */
        var SyllabusForm = function (configdatepicker, configmanageline, fieldfortypeofsubmit) {
            this.configdatepicker = configdatepicker;
            this.configmanageline = configmanageline;
            this.fieldfortypeofsubmit = fieldfortypeofsubmit;

            // Init tabs form.
            this.tabsinit();

            // Set calendar behaviour.
            this.managelines();

            // Init the onclickbuttons function.
            this.onclickbuttons();
        };

        /** @var {Array} The nb repeats array. */
        SyllabusForm.prototype.nbrepeat = [];

        /** @var {Array} The datepicker config. */
        SyllabusForm.prototype.configdatepicker = [];

        /** @var {Array} The manageline config. */
        SyllabusForm.prototype.configmanageline = [];

        /** @var {RequiredfieldsPopup} The instance of RequiredfieldsPopup (the popup to show the list of tabs with errors). */
        SyllabusForm.prototype.requiredfieldspopup = null;

        /** @var {string} The name of the field to identify the type of submit (which submit button was clicked). */
        SyllabusForm.prototype.fieldfortypeofsubmit = '';

        /**
         * Manages lines set up.
         *
         * @name   managelines
         * @return {Void}
         * @function
         */
        SyllabusForm.prototype.managelines = function () {
            var self = this;
            $.each(this.configmanageline, function( index, value ) {
                self.nbrepeat[value.identifier] = parseInt($('input[name="' + value.name + '"]').val());
                self.reorderlines(value.identifier, value.name);
            });

            // Handle on delete click.
            $("#page-mod-syllabus-edit").on('click', 'a.deleteline', function(event) {
                event.preventDefault();

                var identifier = $(this).data('id');
                var repeatname = $(this).data('repeat');
                var element = $(this);
                str.get_strings([
                    {key: 'confirm', component: 'moodle'},
                    {key: 'confirmdeleteline', component: 'mod_syllabus'},
                    {key: 'delete', component: 'moodle'},
                    {key: 'cancel', component: 'moodle'}
                ]).done(function(strings) {
                    notification.confirm(
                        strings[0],
                        strings[1],
                        strings[2],
                        strings[3],
                        function() {
                            var linetodelete = element.closest('.syllabus_repeated_item');
                            linetodelete.remove();
                            self.nbrepeat[identifier]--;
                            // Remove css border if no more item in calendar.
                            if (!self.nbrepeat[identifier]) {
                                $('#' + identifier + '.syllabus_repeated_items_block').removeClass("greyborder");
                            }
                            self.reorderlines(identifier, repeatname);
                        }
                    );
                }).fail(notification.exception);
            });
            // Handle on add click.
            $("#page-mod-syllabus-edit").on('click', '.addline', function(event) {
                event.preventDefault();

                var identifier = $(this).data('id');
                var repeatname = $(this).data('repeat');
                var hiddenBlock = $("#" + identifier + " .hidden");

                hiddenBlock.clone().addClass('newline').insertBefore("#" + identifier + " .hidden");
                $("#" + identifier + " .newline [name^='" + identifier + "_']").each(function() {
                    var name = $(this).attr('name');
                    var patt = /newindex/g;
                    var index = self.nbrepeat[identifier] + 1;
                    var newname = name.replace(patt, index.toString());
                    $(this).attr('name', newname);
                });
                var firstfocusable = $('.newline').find('input,textarea,select').first();
                $('.newline').removeClass('newline hidden');
                // Add css border if calendar was empty.
                if (!self.nbrepeat[identifier]) {
                    $('#' + identifier + '.syllabus_repeated_items_block').addClass("greyborder");
                }
                self.nbrepeat[identifier]++;
                self.reorderlines(identifier, repeatname);
                // Re-apply datepicker.
                Y.use("moodle-form-dateselector",function() {
                    M.form.dateselector.init_date_selectors(self.configdatepicker);
                });
                firstfocusable.focus();
            });
        };

        /**
         * Reorder lines.
         *
         * @name   reorderlines
         * @param {String} id
         * @param {String} namerepeat
         * @return {Void}
         * @function
         */
        SyllabusForm.prototype.reorderlines = function (id, namerepeat) {
            var self = this;
            $("#" + id + " .syllabus_repeated_item:not(.hidden)").each(function(index) {
                $(this).find("[name^='" + id + "_']").each(function() {
                    var name = $(this).attr('name');
                    var patt = /\[[0-9]+\]/g;
                    var newname = name.replace(patt, '[' + index.toString() + ']');
                    $(this).attr('name', newname);
                });
            });
            $('input[name="' + namerepeat + '"]').val(self.nbrepeat[id]);
        };

        /**
         * Syllabus tabs form.
         *
         * @name   tabsinit
         * @return {Void}
         * @function
         */
        SyllabusForm.prototype.tabsinit = function () {
            str.get_strings([
                {key: 'collapseall'},
                {key: 'expandall'}]
                    ).done(
                    function (strings) {
                        var collapseall = strings[0];
                        var expandall = strings[1];
                        // Collapse/Expand all.
                        $(".syllabus").on('click', '.collapsible-actions a', function (event) {
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

                        $(".syllabus").on('click', 'ul.nav-tabs li.nav-item a', function (event) {
                            event.preventDefault();
                            var tabid = $(this).data('tab');
                            $('input[name="rubric"]').val(tabid);
                        });

                        // Single collapse/expand.
                        $('.syllabus legend a.fheader').on('click', function (event) {
                            event.preventDefault();
                            var id_fieldset = $(this).attr('aria-controls');
                            var tabid = $(this).closest('div.tab-pane').attr('id');

                            $('#' + id_fieldset).toggleClass('collapsed');

                            if ($('#' + id_fieldset).hasClass('collapsed')) {
                                $(this).attr('aria-expanded', false);
                            } else {
                                $(this).attr('aria-expanded', true);
                            }

                            var allcollapsed = true;
                            var allexpanded = true;
                            $('.syllabus #' + tabid + ' fieldset.collapsible').each(function () {
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
        };

        /**
         * Initialises the actions execute when one of the form submit buttons is clicked.
         *
         * @name   onclickbuttons
         * @return {Void}
         * @function
         */
        SyllabusForm.prototype.onclickbuttons = function () {
            var self = this;

            // For each submit button.
            $('#page-mod-syllabus-edit [data-submitbtn="true"]').each(function(keysubmit, fieldsubmit) {
                $(fieldsubmit).on('click', function(event) {
                    event.preventDefault();
                    var tabserror = [];
                    // For each required field in the form.
                    $('[data-required="true"]').each(function(key, field) {
                        // Do not check hidden rows used for duplication.
                        if (!$(this).closest('.syllabus_repeated_item').hasClass('hidden')) {
                            // For regular fields.
                            var fieldtovalidate = field;
                            var valuetovalidate = $(field).val();

                            if ($(fieldtovalidate).data('editorfield')) {
                                // For fields with rich text editor.
                                var editorid = '#id_' + $(fieldtovalidate).data('editorfield');
                                if ($(editorid + 'editable').length) {
                                    // If the field with 'editable' suffix exists, use this field instead of the regular fields.
                                    // This is used for the Atto editor only.
                                    fieldtovalidate = editorid + 'editable';
                                    valuetovalidate = $(editorid + 'editable').text();
                                } else if($(editorid + '_parent').length) {
                                    // If the field with '_parent' suffix exists, use this field instead of the regular fields.
                                    // This is used for the TinyMce editor only.
                                    fieldtovalidate = editorid + '_parent .mceIframeContainer';
                                    valuetovalidate = $(editorid + '_ifr').contents().find('body').text();
                                } else {
                                    // This is used for the Plain text area editor.
                                    fieldtovalidate = editorid;
                                    valuetovalidate = $(fieldtovalidate).val();
                                }
                            }

                            // If the field is empty.
                            if (valuetovalidate.trim() == '') {
                                // Show the field as invalid.
                                $(fieldtovalidate).addClass('is-invalid');

                                // Save the name of the tab for this field.
                                var tabname = $(field).parents('[role="tabpanel"]').data('tabname');
                                if ($.inArray(tabname, tabserror) == -1) {
                                    tabserror.push( tabname );
                                }
                            } else {
                                // The field is valid, make sure it is not shown as invalid anymore.
                                $(fieldtovalidate).removeClass('is-invalid');
                            }
                        }
                    });

                    // Some duplicable rubrics must be added at least once.
                    $('[data-morethanone="true"]').each(function(key, field) {
                        if ($(field).val() < 1) {
                            $('[data-repeat="' + $(field).attr('name') + '"]').addClass('is-invalid');
                            var tabname = $(field).data('tabname');
                            if ($.inArray(tabname, tabserror) == -1) {
                                tabserror.push( tabname );
                            }
                        } else {
                            $('[data-repeat="' + $(field).attr('name') + '"]').removeClass('is-invalid');
                        }
                    });

                    // Keep which button was clicked.
                    $('#page-mod-syllabus-edit [name=' + self.fieldfortypeofsubmit + ']').val(event.target.name);
                    if (tabserror.length > 0) {
                        // Show the error popup.
                        requiredfieldspopup.init(tabserror, $(this).parents('form'));
                    } else {
                        // Submit the form.
                        // Temporarily set onbeforeunload to null to avoid the browser popup saying the changes were not saved.
                        var tempunload = window.onbeforeunload;
                        window.onbeforeunload = null;
                        // Call the old onbeforeunload function in case some usefull stuff is done in it.
                        tempunload();
                        // Submit the form and set back the old onbeforeunload.
                        $(this).parents('form').submit();
                        window.onbeforeunload = tempunload;
                    }
                });
            });
        };

        return  {
            init: function (configdatepicker, configmanageline, fieldfortypeofsubmit) {
                new SyllabusForm(configdatepicker, configmanageline, fieldfortypeofsubmit);
            }
        };
    });