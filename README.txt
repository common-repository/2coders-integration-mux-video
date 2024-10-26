=== 2Coders integration for Mux Video ===
Contributors: @2coders
Tags: streaming, player, video hosting, video, video player
Tested up to: 6.4
Requires at least: 5.9
Requires PHP: 7.2
Stable tag: 1.0.3
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This is a short description of the plugin

== Description ==

The 2Coders integration for Mux Video is your go-to integration for WordPress, making video uploads and streaming a breeze. By seamlessly combining 2Coders integration for Mux Video with WordPress, you can supercharge your website's video capabilities. 

Our plugin empowers WordPress users of all experience levels, from content creators to website administrators, to effortlessly upload, manage, and stream videos with top-notch quality and

= Key Features = 

- Effortless Video Uploads and Previews: Say goodbye to video upload complications. The 2Coders integration for Mux Video offers a user-friendly interface within your WordPress dashboard, simplifying video uploads and management.
- Optimized Video Streaming: Mux's advanced technology ensures your videos are delivered at their best. With adaptive streaming and automatic optimization, your audience can enjoy seamless playback regardless of their connection.
- Customizable Player: Personalize the video player to match your website's look and feel. With customizable player themes and options, your videos seamlessly blend into your site's design.
- Developer-Friendly: For developers, the 2Coders integration for Mux Video offers customization and integration features, making it a perfect fit for your existing workflows.

= More Features =

- Drag & Drop files with one click to upload your assets
- Preview using Mux player
- Manage your content
- Hassle-free streaming - Immediate synchronization with your Mux account and Player
- Insert videos with ease anywhere through shortcodes
- Easily edit entry data - Preview video, add title, description and cover
- Custom stream domains - Stream videos using Mux with the domain of your choice
- Secure video streaming - Protected streaming using signed URLs

= Compatible with your favorite themes, page builders and plugins =
No matter what theme or plugins you use, 2Coders integration for Mux Video has you covered. Check out just a few of the popular products Smush is working with to help make your site faster and more efficient:

== Installation ==

= Minimum Requirements =

* WordPress 5.9 or greater
* PHP version 7.2 or greater
* MySQL version 5.0 or greater

= We recommend your host supports: =

* PHP version 7.4 or greater
* MySQL version 5.6 or greater
* WordPress Memory limit of 64 MB or greater (128 MB or higher is preferred)

== Frequently Asked Questions ==

= Is it necessary to have a Mux account? =

You will need to have an active account on the Mux platform and generate an Access Token to link your account. 

= How do I get my Access Tokens? =

The Mux Video API uses a token key pair that consists of a Access Token ID and Secret Key for authentication. Unless you have already done so, you can generate a new Access Token in the [Access Token settings](https://dashboard.mux.com/settings/access-tokens) of your Mux account dashboard.

= How to synchronize uploaded videos with my mux account =

By entering the Access Tokens your account will be synchronized in real time. All the videos you upload in the Mux platform, you will see them in your asset list and vice versa.

== Screenshots ==

1. Plugin Settings page to set your API Keys and sync your Mux account
2. Plugin Asset list page to show all assets uploaded
3. Plugin Upload page to upload new assets
4. Insert asset using shortcode anywhere

== Changelog ==
= 1.0.0 =
* Initial release

== Service Information ==

Mux Service: https://www.mux.com/
Terms of Use: https://www.mux.com/terms
Privacy Policy: https://www.mux.com/privacy

== 3rd Party or External Services

This plugin utilizes several libraries and external services for its functionality. Below are the details:

=== Mux PHP SDK

The plugin makes use of the Mux PHP SDK library to interact with the Mux API.

- [Mux PHP SDK on GitHub](https://github.com/muxinc/mux-php)

=== Mux Player

The video playback functionality is based on Mux Player, a video player from Mux.

- [Mux Player on Mux's website](https://mux.com/video-player)
- Terms of Service: [Mux Terms of Service](https://mux.com/terms)

=== PHP JWT

The PHP JWT library is used for generating and manipulating JWT tokens.

- [PHP JWT on GitHub](https://github.com/firebase/php-jwt)
- License: [PHP JWT License](https://github.com/firebase/php-jwt/blob/master/LICENSE)

=== Mux Upchunk

The file upload functionality utilizes Mux Upchunk, a library to facilitate file uploads.

- [Mux Upchunk on GitHub](https://github.com/muxinc/upchunk)

=== Font Awesome 5

Font Awesome is a font and icon toolkit based on CSS and Less. It provides scalable vector icons that can be customized with CSS.

- **Version:** 5.15.4
- **Website:** [Font Awesome](https://fontawesome.com/)
- **License:** [Font Awesome Free License](https://fontawesome.com/license/free)

== Important Notice: ==

This plugin relies on the following domains for its core functionality:
https://stream.mux.com/ - Used for streaming video content.
https://api.mux.com - Provides API access for plugin communication.
https://image.mux.com/ - Used for fetching and displaying video thumbnails.

== Disclaimer ==
This plugin is provided as-is, and the developers are not responsible for the performance or actions of the Mux service. Users are encouraged to review Mux's terms of use and privacy policies before implementing the plugin.