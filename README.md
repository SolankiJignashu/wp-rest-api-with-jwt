=== WordPress REST API WITH JWT TOKEN ===

Contributors:      Jignashu Solanki
Tags:              REST API, JWT
Tested up to:      6.0
Stable tag:        1.0.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Plugin allows to activate and deactivate user using REST API. It requires jwt token in Payload which consist of email_id as key and user_email as value pair. 

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Changelog ==

= 1.0.0 =
* Release


== CURL Example for User Activate == 

`curl --location --request POST 'https://example.com/wp-json/wp/v1/user_activate' \
--header 'payload: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImppZ25hc2h1LnNvbGFua2lAd2hpdGVoYXRqci5jb20ifQ.riQciAWuy7-hhttAMCwRLaxm0_zXlSOpoY37-77vi1U'`
