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
 * Manage the popup about the required fields.
 *
 * @package    mod_syllabus
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2020 Université de Montréal
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/templates', 'core/notification'],
    function($, Str, ModalFactory, ModalEvents, Templates, Notification) {

        /**
         * Constructor.
         *
         * @param {array} tabserror The list of tabs with errors (to list the tabs names in the popup).
         * @param {object} editform The edit form that this popup lists the errors of.
         *
         * Each call to init gets it's own instance of this class.
         */
        var RequiredfieldsPopup = function(tabserror, editform) {
            this.init(tabserror, editform);
        };

        /**
         * @var {Modal} modal
         * @private
         */
        RequiredfieldsPopup.prototype.modal = null;

        /**
         * Initialise the class.
         *
         * @param {array} tabserror The list of tabs with errors (to list the tabs names in the popup).
         * @param {object} editform The edit form that this popup lists the errors of.
         * @private
         * @return {Promise}
         */
        RequiredfieldsPopup.prototype.init = function(tabserror, editform) {
            var self = this;
            // Fetch the title string.
            return Str.get_string('requiredfields_title', 'mod_syllabus').then(function(title) {
                // Create the modal.
                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: title
                });
            }.bind(this)).then(function(modal) {
                // Keep a reference to the modal.
                self.modal = modal;

                // Forms are big, we want a big modal.
                self.modal.setLarge();

                // We want to reset the form every time it is opened.
                self.modal.getRoot().on(ModalEvents.shown, function() {
                    var params = {};
                    if (tabserror.length > 1) {
                        params.hasmoreonetab = true;
                        params.hasonetab = false;
                    } else {
                        params.hasmoreonetab = false;
                        params.hasonetab = true;
                    }
                    params.tabs = tabserror;

                    Templates.render('mod_syllabus/requiredfields', params).done(function(html) {
                        self.modal.setBody(html);
                    }).fail(Notification.exception);

                }.bind(this));

                // Clicking on the save button submits the form (and continues with usual behaviour).
                self.modal.getRoot().on(ModalEvents.save, function() {
                    // Temporarily set onbeforeunload to null to avoid the browser popup saying the changes were not saved.
                    var tempunload = window.onbeforeunload;
                    window.onbeforeunload = null;
                    // Call the old onbeforeunload function in case some usefull stuff is done in it.
                    tempunload();
                    // Submit the form and set back the old onbeforeunload.
                    $(editform).submit();
                    window.onbeforeunload = tempunload;
                }.bind(this));

                // Click on the cancel button.
                self.modal.getRoot().on(ModalEvents.cancel, function() {
                    // Destroy the popup and stay on the form without saving.
                    self.modal.destroy();
                }.bind(this));

                // Click on the close button.
                self.modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy the popup and stay on the form without saving.
                    self.modal.destroy();
                }.bind(this));

                // Show the popup.
                self.modal.show();
                return self.modal;
            }.bind(this));
        };

        return {
            /**
             * Attach event listeners to initialise this module.
             *
             * @method init
             * @param {array} tabserror The list of tabs with errors (to list the tabs names in the popup).
             * @param {object} editform The edit form that this popup lists the errors of.
             * @return {RequiredfieldsPopup} A new instance of RequiredfieldsPopup.
             */
            init: function(tabserror, editform) {
                return new RequiredfieldsPopup(tabserror, editform);
            }
        };
    });