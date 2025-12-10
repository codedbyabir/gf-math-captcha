=== GF Math Captcha - Robust Reload ===
Contributors: Nexiby LLC
Tags: gravity forms, captcha, math captcha, spam protection
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author: Nexiby LLC
Author URI: https://nexiby.com
Description: GF Math Captcha is a lightweight, server-side Math CAPTCHA plugin for Gravity Forms. It adds simple addition-based math questions to your forms to prevent spam submissions. The plugin automatically generates new random numbers on every page load. Fully compatible with HTML fields, hidden fields, and custom admin labels. No JavaScript required.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create a Gravity Form or open an existing one.
4. Add the following fields to your form:
   - **Hidden Field** with Admin Label `num1` (or first hidden field as fallback)
   - **Hidden Field** with Admin Label `num2` (or second hidden field as fallback)
   - **HTML Field** containing the placeholder `{math_question}`
   - **Single Line Text Field** with Admin Label `math_answer` (or first text field as fallback)
5. Save the form. The math CAPTCHA will now automatically appear in your form.

== Frequently Asked Questions ==

= Can I use this plugin with multiple Gravity Forms? =
Yes, the plugin will work with any form on your site. It automatically detects the required hidden fields, HTML field, and answer field. If you want to limit the CAPTCHA to specific forms, you can customize the plugin by adding the form ID check.

= Can I change the number range? =
Yes, by default numbers range from 1 to 10. You can modify the `rand(1, 10)` calls in the plugin code to change the minimum and maximum values.

= Can I use subtraction or multiplication? =
Currently, the plugin supports addition only. You can extend the plugin by modifying the question generation and validation logic.

= What happens if a user submits a wrong answer? =
If the answer is incorrect, the form validation will fail, display an error message, and automatically generate a new math question.

== Screenshots ==

1. Gravity Form with Math CAPTCHA question displayed in HTML field.
2. User submits wrong answer, new numbers are generated.
3. Correct answer allows form submission.

== Changelog ==

= 1.0 =
* Initial release.

== License ==

This plugin is licensed under the GPL v2 (or later).

