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

require_once("../../config.php");
require_once("lib.php");


$id = required_param('id', PARAM_INT);    // Course Module ID.

if (! $cm = get_coursemodule_from_id('medicalimageviewer', $id)) {
    print_error("Course Module ID was incorrect");
}

if (! $course = $DB->get_record("course", array('id' => $cm->course))) {
    print_error("Course is misconfigured");
}

$context = context_module::instance($cm->id);

require_login($course, true, $cm);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$entriesmanager = has_capability('mod/medicalimageviewer:manageentries', $context);
$canadd = has_capability('mod/medicalimageviewer:addentries', $context);

if (!$entriesmanager && !$canadd) {
    print_error('accessdenied', 'medicalimageviewer');
}

if (! $medicalimageviewer = $DB->get_record("medicalimageviewer", array("id" => $cm->instance))) {
    print_error("Course module is incorrect");
}

if (! $cw = $DB->get_record("course_sections", array("id" => $cm->section))) {
    print_error("Course module is incorrect");
}

$medicalimageviewername = format_string($medicalimageviewer->name, true, array('context' => $context));

// Header.
$PAGE->set_url('/mod/medicalimageviewer/view.php', array('id' => $id));
$PAGE->navbar->add($medicalimageviewername);
$PAGE->set_title($medicalimageviewername);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($medicalimageviewername);

// Check to see if groups are being used here.
$groupmode = groups_get_activity_groupmode($cm);
$currentgroup = groups_get_activity_group($cm, true);
groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/medicalimageviewer/view.php?id=$cm->id");

if ($entriesmanager) {
    $entrycount = medicalimageviewer_count_entries($medicalimageviewer, $currentgroup);
    echo '<div class="reportlink"><a href="report.php?id='.$cm->id.'">'.
          get_string('viewallentries', 'medicalimageviewer', $entrycount).'</a></div>';
}

$medicalimageviewer->intro = trim($medicalimageviewer->intro);
if (!empty($medicalimageviewer->intro)) {
    $intro = format_module_intro('medicalimageviewer', $medicalimageviewer, $cm->id);
    echo $OUTPUT->box($intro, 'generalbox', 'intro');
}

echo '<br />';

$timenow = time();
if ($course->format == 'weeks' and $medicalimageviewer->days) {
    $timestart = $course->startdate + (($cw->section - 1) * 604800);
    if ($medicalimageviewer->days) {
        $timefinish = $timestart + (3600 * 24 * $medicalimageviewer->days);
    } else {
        $timefinish = $course->enddate;
    }
} else {  // Have no time limits on the medicalimageviewers.

    $timestart = $timenow - 1;
    $timefinish = $timenow + 1;
    $medicalimageviewer->days = 0;
}
if ($timenow > $timestart) {

    echo $OUTPUT->box_start();

    // Edit button.
    if ($timenow < $timefinish) {

        if ($canadd) {
            echo $OUTPUT->single_button('edit.php?id='.$cm->id, get_string('startoredit', 'medicalimageviewer'), 'get',
                array("class" => "singlebutton medicalimageviewerstart"));
        }
    }

    // Display entry.
    if ($entry = $DB->get_record('medicalimageviewer_entries', array('userid' => $USER->id, 'medicalimageviewer' => $medicalimageviewer->id))) {
        if (empty($entry->text)) {
            echo '<p align="center"><b>'.get_string('blankentry', 'medicalimageviewer').'</b></p>';
        } else {
            echo medicalimageviewer_format_entry_text($entry, $course, $cm);
        }
    } else {
        echo '<span class="warning">'.get_string('notstarted', 'medicalimageviewer').'</span>';
    }

    echo $OUTPUT->box_end();

    // Info.
    if ($timenow < $timefinish) {
        if (!empty($entry->modified)) {
            echo '<div class="lastedit"><strong>'.get_string('lastedited').': </strong> ';
            echo userdate($entry->modified);
            echo ' ('.get_string('numwords', '', count_words($entry->text)).')';
            echo "</div>";
        }
        // Added three lines to mark entry as being dirty and needing regrade.
        if (!empty($entry->modified) AND !empty($entry->timemarked) AND $entry->modified > $entry->timemarked) {
            echo "<div class=\"lastedit\">".get_string("needsregrade", "medicalimageviewer"). "</div>";
        }

        if (!empty($medicalimageviewer->days)) {
            echo '<div class="editend"><strong>'.get_string('editingends', 'medicalimageviewer').': </strong> ';
            echo userdate($timefinish).'</div>';
        }

    } else {
        echo '<div class="editend"><strong>'.get_string('editingended', 'medicalimageviewer').': </strong> ';
        echo userdate($timefinish).'</div>';
    }

    // Feedback.
    if (!empty($entry->entrycomment) or !empty($entry->rating)) {
        $grades = make_grades_menu($medicalimageviewer->grade);
        echo $OUTPUT->heading(get_string('feedback'));
        medicalimageviewer_print_feedback($course, $entry, $grades);
    }

} else {
    echo '<div class="warning">'.get_string('notopenuntil', 'medicalimageviewer').': ';
    echo userdate($timestart).'</div>';
}


// Trigger module viewed event.
$event = \mod_medicalimageviewer\event\course_module_viewed::create(array(
   'objectid' => $medicalimageviewer->id,
   'context' => $context
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('medicalimageviewer', $medicalimageviewer);
$event->trigger();

echo $OUTPUT->footer();
