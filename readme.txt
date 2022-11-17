=== Generate Dynamic Images - Bannerbear ===
Contributors: yongfook
Tags: open graph, og, image, dynamic image, widget
Requires at least: 4.9
Tested up to: 6.1
Requires PHP: 5.6
Version: 1.0.0
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add dynamically generated open graph images or pinterest pins to posts and pages automatically.

== Description ==

A WordPress plugin that adds Bannerbear Signed URL functionality to WordPress sites.

### What is Bannerbear?

[Bannerbear](https://www.bannerbear.com/) is a SaaS service that auto-generates images, based on dynamic parameters that you send, and templates you have set up on the Bannerbear back end.

### What does this plugin do?

This plugin provides an easy interface to add Bannerbear Signed URL images to your WP theme.

Bannerbear Signed URLs are dynamic urls that *generate images on the fly* based on templates. This plugin helps you map WP variables (title, date etc) to templates, and then inserts those URLs into WP posts and pages. 

One use case for this is auto-generating Open Graph images.

### Who is this plugin for?

This plugin requires a Bannerbear Scale or Enterprise account and is best suited for agencies managing WordPress sites for multiple clients, or individual users with large WordPress sites.

== Usage ==

**Pre-requisite**: Create a Bannerbear project and add a template (or duplicate this [sample template](https://app.bannerbear.com/p/B2zYp0bOvEKD9J5mVZ)) to your project.

- Go to **Bannerbear Plugin**.
- Select **"Add New"** and paste your Bannerbear Project [API Key](https://www.bannerbear.com/help/articles/64-where-do-i-get-my-api-key/).
- Select a template from your list of templates.
- This will automatically create and grab a [Signed URL Base](https://www.bannerbear.com/help/articles/179-generate-images-using-signed-urls/) from the template, and all available template modifications will be listed.

Each API modifiable layer will have a dropdown menu that can be mapped with fields from the WordPress site. Select layers that you want to modify and choose where you want to apply the template/Signed URL.

You can then embed it as a shortcode/snippet or add as a Block on WordPress pages. 

== Installation ==

* Go to WordPress Dashboard.
* Click on Plugins -> Add New
* Search for "**Bannerbear**"
* Find the plugin and click on the Install Now button
* After installation, click on Activate Plugin link to activate the plugin.


== Frequently Asked Questions ==

= What is required to use this plugin? =

You will need to create an account on [our website](https://www.bannerbear.com/) in order to get an API Key.

= Where can I get support? =

Use the [contact form on our website](https://www.bannerbear.com/support/).

== Screenshots ==

1. Bannerbear plugin page
2. Enter an API key
3. Select a template
4. Assign dynamic fields to a template
5. Embed instructions
6. Bannerbear block
7. Post with dynamic image
8. Open Graph image exmpale

== Changelog ==

= 1.0.0 =
* Initial release
