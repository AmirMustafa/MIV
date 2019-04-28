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
Global $COURSE;
$your_variable = $COURSE->id;

$id = required_param('id', PARAM_INT);    // Course Module ID.

if (! $cm = get_coursemodule_from_id('medicalimageviewer', $id)) {
    print_error("Course Module ID was incorrect");
}



// require_login($course, true, $cm);





echo $OUTPUT->header();





/*==========================VIEW PAGE: START ======================*/
$course    = $cm->course;
$cmid = $_GET['id'];
$sql  = "SELECT itemid, image FROM {medicalimageviewer_images} WHERE course = $course AND coursemodule = $cmid ORDER BY time_created DESC LIMIT 1";
$currentimageSQL = $DB->get_record_sql($sql);

/*echo "<pre>";
print_r($currentimageSQL);die;*/
$currentitemid = $currentimageSQL->itemid;
$currentitemid = isset($currentitemid) ? $currentitemid : 'NA';

$currentimage = $currentimageSQL->image;
$currentimage = isset($currentimage) ? $currentimage : 'NA';

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    list($baselink, $otherlink) = explode('/mod', $actual_link);
$currenturl = 'NA';
if($currentimage != 'NA') {
    
    $currenturl = $baselink . "/draftfile.php/5/user/draft/$currentitemid/$currentimage";
    include_once 'viewimages.php';

    // echo "<img src=$currenturl width='40%' />";

    // for testing
    /*echo "<img src='http://localhost/moodle_aman/draftfile.php/5/user/draft/755471004/medicalimageviewer.jpg' width='40%' />";die;*/
} 
else {
    include_once 'validation.php';
}
// include_once 'viewimages.php';
/*==========================VIEW PAGE: END ======================*/


echo $OUTPUT->footer();
