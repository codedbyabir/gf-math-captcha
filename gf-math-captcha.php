<?php
/*
Plugin Name: Simple GF Math Captcha
Description: Adds a lightweight, server-side Math CAPTCHA to Gravity Forms for enhanced spam protection. Automatically generates new random numbers on every page load. Fully compatible with HTML fields, hidden fields, and custom admin labels. No JavaScript required.
Version: 1.0
Author: Nexiby LLC
Author URI: https://nexiby.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.0
Requires PHP: 7.4
Tags: gravity forms, captcha, math captcha, spam protection
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate numbers before render or validation, but do NOT overwrite posted values.
 */
add_filter('gform_pre_render', 'gf_math_captcha_generate');
add_filter('gform_pre_validation', 'gf_math_captcha_generate');

function gf_math_captcha_generate($form)
{

    // map adminLabel => field object
    $fields_by_admin = [];
    foreach ($form['fields'] as $field) {
        if (!empty($field->adminLabel)) {
            $fields_by_admin[$field->adminLabel] = $field;
        }
    }

    // find IDs for num1 and num2 if provided
    $num1_field = isset($fields_by_admin['num1']) ? $fields_by_admin['num1'] : null;
    $num2_field = isset($fields_by_admin['num2']) ? $fields_by_admin['num2'] : null;

    // If num1 field exists and there's no posted value, set default
    if ($num1_field) {
        $input_name = 'input_' . $num1_field->id;
        $posted = rgpost($input_name);
        if ($posted === null || $posted === '') {
            $num = rand(1, 10);
            $num1_field->defaultValue = $num;
        }
    }

    if ($num2_field) {
        $input_name = 'input_' . $num2_field->id;
        $posted = rgpost($input_name);
        if ($posted === null || $posted === '') {
            $num = rand(1, 10);
            $num2_field->defaultValue = $num;
        }
    }

    // Replace {math_question} in HTML field(s)
    foreach ($form['fields'] as &$field) {
        if ($field->type === 'html' && strpos($field->content, '{math_question}') !== false) {

            // Prefer posted values if available (so the question shown matches posted hidden fields)
            $n1 = null;
            $n2 = null;

            if ($num1_field) {
                $posted_n1 = rgpost('input_' . $num1_field->id);
                $n1 = ($posted_n1 !== null && $posted_n1 !== '') ? $posted_n1 : $num1_field->defaultValue;
            }

            if ($num2_field) {
                $posted_n2 = rgpost('input_' . $num2_field->id);
                $n2 = ($posted_n2 !== null && $posted_n2 !== '') ? $posted_n2 : $num2_field->defaultValue;
            }

            // Fallbacks
            if ($n1 === null)
                $n1 = rand(1, 10);
            if ($n2 === null)
                $n2 = rand(1, 10);

            $question_html = "<strong>Solve this: {$n1} + {$n2} =</strong>";
            $field->content = str_replace('{math_question}', $question_html, $field->content);
        }
    }

    return $form;
}


/**
 * Validate the math answer on form submission.
 */
add_filter('gform_validation', 'gf_math_captcha_validate');

function gf_math_captcha_validate($validation_result)
{
    $form = $validation_result['form'];

    // find fields by adminLabel
    $num1_id = null;
    $num2_id = null;
    $answer_id = null;

    foreach ($form['fields'] as $field) {
        if (!empty($field->adminLabel)) {
            if ($field->adminLabel === 'num1')
                $num1_id = $field->id;
            if ($field->adminLabel === 'num2')
                $num2_id = $field->id;
            if ($field->adminLabel === 'math_answer')
                $answer_id = $field->id;
        }
    }

    // if any required IDs missing, just return (no validation)
    if (!$num1_id || !$num2_id || !$answer_id) {
        return $validation_result;
    }

    // get posted values
    $n1 = rgpost('input_' . $num1_id);
    $n2 = rgpost('input_' . $num2_id);
    $ans = rgpost('input_' . $answer_id);

    // normalize
    $n1 = is_numeric($n1) ? intval($n1) : null;
    $n2 = is_numeric($n2) ? intval($n2) : null;
    $ans = is_numeric($ans) ? intval($ans) : null;

    // If any are null, fail validation (specially the answer)
    $is_valid = true;
    if ($n1 === null || $n2 === null || $ans === null) {
        $is_valid = false;
    } else {
        if (($n1 + $n2) !== $ans) {
            $is_valid = false;
        }
    }

    if (!$is_valid) {
        $validation_result['is_valid'] = false;
        // mark the answer field as failed
        foreach ($validation_result['form']['fields'] as &$field) {
            if ($field->id == $answer_id) {
                $field->failed_validation = true;
                $field->validation_message = 'Incorrect math answer. Please try again.';
                break;
            }
        }
        $validation_result['form'] = $validation_result['form'];
    }

    return $validation_result;
}
