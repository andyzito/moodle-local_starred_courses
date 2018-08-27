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
 * starred_courses block settings
 *
 * @package    local_starred_courses
 * @copyright  2018 onwards Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

define('STARRED_COURSES_USER_PREFERENCE_NAME', 'starred_courses');

function local_starred_courses_extend_navigation_course($navigation) {
    echo "<pre>";
    // $node = new navigation_node('Star current course');
    // $node->showinflatnavigation = true;
    // $test = $navigation->add('TESTLINK', new moodle_url('/local/starred_courses/settings.php'), global_navigation::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    // $test = $navigation->add_node($node, 'coursebadges');
    // $test->showinflatnavigation = true;
    // $test->make_active();
    // print_r($test);
    // $coursenode = $navigation->find('3', navigation_node::TYPE_COURSE);
    // $coursenode->remove();
    echo "</pre>";
}

function local_starred_courses_extend_navigation($navigation) {
    $coursenode = $navigation->find('3', navigation_node::TYPE_COURSE);
    if ($coursenode) {
        $node = $navigation->create('Star this course', '#', navigation_node::TYPE_SETTING, null, null, new pix_icon('star', '', 'local_starred_courses'));
        $add = $coursenode->add_node($node, 'participants');
    }
}

// function local_starred_courses_extend_navigation_course($nav) {
    // print_r($nav);
    // require_login($COURSE->id);($nav)
    // if (isset($COURSE) && $COURSE->id > 1) {
    //     $userctx = \context_user::instance($USER);
    //     // $coursectx = \context_course::instance($COURSE);
    //     has_capability('local/starred_courses:canstar', $userctx);
    // }
// }

// function local_starred_courses_extend_navigation_course($navigation, $course, $context) {
//     // if (has_capability('report/forum:view', $context)) {
//         $url = new moodle_url('/local/starred_courses/settings.php', array('course'=>$course->id));
//         $navigation->add(get_string('pluginname','report_forum'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
//     // }
// }


function initialize_starred_courses_user_preference($userid) {
    if (! get_user_preferences(STARRED_COURSES_USER_PREFERENCE_NAME, false, $userid)) {
        set_user_preference(STARRED_COURSES_USER_PREFERENCE_NAME, '', $userid);
    }
}

function course_is_starred($userid, $courseid) {
    if ($starred = get_starred_course_ids($userid)) {
        return in_array($courseid, $starred);
    }
    return false;
}

function star_course($userid, $courseid) {
    if ($starred = get_starred_course_ids($userid)) {
        if (! in_array($courseid, $starred)) {
            $starred[] = $courseid;
            $starred = implode(',', array_filter($starred));
            return set_user_preference(STARRED_COURSES_USER_PREFERENCE_NAME, $starred, $userid);
        }
    }
    return false;
}

function unstar_course($userid, $courseid) {
    if ($starred = get_starred_course_ids($userid)) {
        if (($key = array_search($courseid, $starred)) !== false) {
            unset($starred[$key]);
            $starred = implode(',', array_filter($starred));
            return set_user_preference(STARRED_COURSES_USER_PREFERENCE_NAME, $starred, $userid);
        }
    }
    return false;
}

function get_starred_course_ids($userid) {
    $starred = get_user_preferences(STARRED_COURSES_USER_PREFERENCE_NAME, false, $userid);
    if ($starred = explode(',', $starred)) {
        return $starred;
    }
    return false;
}

function get_starred_courses($userid) {
    global $DB;

    $starred_courses = array();
    if ($starred_ids = get_starred_course_ids($userid)) {
        foreach ($starred_ids as $courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
            $starred_courses[] = $course;
        }
    }
    return $starred_courses;
}
