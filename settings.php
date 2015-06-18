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
 * Paypal enrolments plugin settings and presets.
 *
 * @package    enrol_paypalenhanced
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter - based on code by Petr Skoda and others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- settings ------------------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_paypalenhanced_settings', '', get_string('pluginname_desc', 'enrol_paypalenhanced')));

    $settings->add(new admin_setting_configtext('enrol_paypalenhanced/paypalbusiness', get_string('businessemail', 'enrol_paypalenhanced'), get_string('businessemail_desc', 'enrol_paypalenhanced'), '', PARAM_EMAIL));

    $settings->add(new admin_setting_configcheckbox('enrol_paypalenhanced/mailstudents', get_string('mailstudents', 'enrol_paypalenhanced'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_paypalenhanced/mailteachers', get_string('mailteachers', 'enrol_paypalenhanced'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_paypalenhanced/mailadmins', get_string('mailadmins', 'enrol_paypalenhanced'), '', 0));

    // Note: let's reuse the ext sync constants and strings here, internally it is very similar,
    //       it describes what should happen when users are not supposed to be enrolled any more.
    $options = array(
        ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
        ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'),
        ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
    );
    $settings->add(new admin_setting_configselect('enrol_paypalenhanced/expiredaction', get_string('expiredaction', 'enrol_paypalenhanced'), get_string('expiredaction_help', 'enrol_paypalenhanced'), ENROL_EXT_REMOVED_SUSPENDNOROLES, $options));

    //--- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_paypalenhanced_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                     ENROL_INSTANCE_DISABLED => get_string('no'));
    $settings->add(new admin_setting_configselect('enrol_paypalenhanced/status',
        get_string('status', 'enrol_paypalenhanced'), get_string('status_desc', 'enrol_paypalenhanced'), ENROL_INSTANCE_DISABLED, $options));

    $settings->add(new admin_setting_configtext('enrol_paypalenhanced/cost', get_string('cost', 'enrol_paypalenhanced'), '', 0, PARAM_FLOAT, 4));

    $paypalcurrencies = enrol_get_plugin('paypalenhanced')->get_currencies();
    $settings->add(new admin_setting_configselect('enrol_paypalenhanced/currency', get_string('currency', 'enrol_paypalenhanced'), '', 'USD', $paypalcurrencies));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_paypalenhanced/roleid',
            get_string('defaultrole', 'enrol_paypalenhanced'), get_string('defaultrole_desc', 'enrol_paypalenhanced'), $student->id, $options));
    }

    $settings->add(new admin_setting_configduration('enrol_paypalenhanced/enrolperiod',
        get_string('enrolperiod', 'enrol_paypalenhanced'), get_string('enrolperiod_desc', 'enrol_paypalenhanced'), 0));

    if (!during_initial_install()) {

        // Load all courses to show in the selectors
        $courses = $DB->get_records('course', null, 'fullname', 'id, fullname');
        $courselist = array(0 => '');
        foreach ($courses as $course) {
            $courselist[$course->id] = $course->fullname;
        }

        // Prerequisite courses
        $settings->add(new admin_setting_configmultiselect('enrol_paypalenhanced/prerequisitecourses', get_string('prerequisitecourses', 'enrol_paypalenhanced'), get_string('prerequisitecourses_desc', 'enrol_paypalenhanced'), array(), $courselist));

        // Conflicting courses
        $settings->add(new admin_setting_configmultiselect('enrol_paypalenhanced/conflictingcourses', get_string('conflictingcourses', 'enrol_paypalenhanced'), get_string('conflictingcourses_desc', 'enrol_paypalenhanced'), array(), $courselist));

        // Bundled courses
        //$settings->add(new admin_setting_configmultiselect('enrol_paypalenhanced/bundledcourses', get_string('bundledcourses', 'enrol_paypalenhanced'), get_string('bundledcourses_desc', 'enrol_paypalenhanced'), array(), $courselist));

    }

}
