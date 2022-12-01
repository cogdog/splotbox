# SPLOTbox Wordpress Theme
by Alan Levine https://cog.dog or http://cogdogblog.com/

[![Wordpress version badge](https://img.shields.io/badge/version-4.1.4-green.svg)](https://github.com/cogdog/tru-collector/blob/master/style.css)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

:house: SPLOTbox
[:mag: Examples](examples.md) | 
[:rocket: Installing](install.md) | 
[:book: Documentation](docs.md) | 
[:speech_balloon: Discussions](https://github.com/cogdog/splotbox/discussions)

![Sample SPLOTbox Site](screenshot.png "Sample SPLOTbox Site")

This Wordpress Theme is a [SPLOT](http://splot.ca/)  and enables you to build sites like the [demo box](http://splot.ca/box) for that allows visitors to add to your collection of media content (termed "items") where contributions can be made without requiring logins or providing personally identifying information. 

The theme options  allow you to offer a field to add  media by one URL from the following services,  [a subset of ones WordPress supports by Embed](https://wordpress.org/support/article/embeds/#list-of-sites-you-can-embed-from): 

* Flickr photos
* Giphy animated GIFs 
* Mixcloud audio 
* Soundcloud audio 
* Slideshare presentations
* SpeakerDeck presentations
* TED Talks video 
* Vimeo video 
* YouTube video
* TikTok videos

In addition, special code has been added to SPLOTbox to support:

* Adobe Express (formerly "Spark") Pages and Videos
* Internet Archive audio or video
* Loom screencasts
* Sodaophonic audio
* Vocaroo audio

Media can also be added by any direct URL to audio in mp3, ogg, or m4a formats or  jpg, png, or gif images. Site owners can also enable an upload button for image and sound files in mp3, ogg, or m4a (audio) or jpg, png, gif (image) as well as a using a built in audio recorder.

![](images/media-method.gif)

![](images/splot-recorder.gif)

After clicking the microphone icon, if you do not see a dialog box asking to grant access to the microphone or do not see the recording controls, verify that the address of the web page for the form starts with `https`. 

Other features that can be activated in SPLOTbox are to provide entry fields for (and require or not), descriptions/cations, a credit source name and a selection of reuse licenses.

SPLOTbox is a huge improvement and enhancement of the first generation [TRU Sounder SPLOT](https://github.com/cogdog/splot-sounder). 

If you have problems, feature suggestions, questions piles of unmarked bills to send my way, please [contact me via the discussions area](https://github.com/cogdog/splotbox/discussions/) on this repo.

For more info about SPLOTbox see

* [SPLOTbox](https://splot.ca/splots/splotbox) the original home if there is one (splot.ca)
* [Overly detailed blog posts About TRU Collector](https://cogdogblog.com/tag/splotbox/) (cogdogblog.com)
* [Talk About TRU Collector](https://github.com/cogdog/splotbox/discussions) (Github Discussions)


## So You are Interested in making your oewn SPLOTbox site?

That's fantabulous!

To give you an diea what it can do I have [a collection of other sites](examples.md) using this theme, then provide  [details on how to install it](install.md), and once set up, the [documentation](docs.md) for customizing it in WordPress (plus details on how to update). The same documentation is available in the theme options and also in a more readable format - [see the Docs!](https://docsify-this.net/?basePath=https://raw.githubusercontent.com/cogdog/splotbox/master&homepage=docs.md&sidebar=true#/) (thanks to [Docsify This](https://docsify-this.net/)).

Again, if you have questions, please make use of the [Discussion Area](https://github.com/cogdog/splotbox/discussions).

## With Thanks

SPLOTs have no venture capital, no IPOs, no real funding at all. But they have been helped along by a few groups worth recognizing with an icon and a link.

SPLOTbox was evolved from an earlier [Sound Collector](http://splot.ca/splots/tru-sounder/) created under [Thompson Rivers University Open Learning Fellowship](http://cogdog.trubox.ca/). Incentive for the first SPLOTbox was provided with a small seed grant from the [University of Saskatchewan](http://usask.ca/). Further development was supported in part by a [Reclaim Hosting Fellowship](http://reclaimhosting.com), an [OpenETC grant](https://opened.ca), and ongoing support by [Patreon patrons](https://patreon.com/cogdog).

[![Thompson Rivers University](https://cogdog.github.io/images/tru.jpg)](https://tru.ca) [![University of Saskatchewan](https://cogdog.github.io/images/usask.jpg)](https://usask.ca)  [![Reclaim Hosting](https://cogdog.github.io/images/reclaim.jpg)](https://reclaimhosting.com) [![OpenETC](https://cogdog.github.io/images/openetc.jpg)](https://opened.ca)   [![Supporters on Patreon](https://cogdog.github.io/images/patreon.jpg)](https://patreon.com/cogdog) 

*If this kind of stuff has any value to you, please consider supporting me so I can do more!*

[![Support me on Patreon](http://cogdog.github.io/images/badge-patreon.png)](https://patreon.com/cogdog) [![Support me on via PayPal](http://cogdog.github.io/images/badge-paypal.png)](https://paypal.me/cogdog)


## Relatively Cool New Features & Updates

* (4.0)  Support for Sodaphonic audio, minor typo fixes, minr CSS tweaks
* (3.95) SPLOTbox Options provides settings to change the sorting of published items to be by date or alphabetical, and can be ascending or descending. There are also settings to apply it only to the home page, or just to tag and/or category archives. Also updated template to properly display archive headings and descriptions (for tag and category archives). Added shortcodes for listing all (or with other options some) tags used plus a shortcode to display the total number of items published in a box
* (3.92) Customizer options created for all metadata fields in a single item view. Also, new theme options added to enable use of categories or tags by admin editing only (they are not options on the share form, but can be edited in the Dashboard).
* (3.9) Tested for support of Kaltura video via SPLOTbox Extender plugin, archive for media types identified via post formats, css for taxonomy widget to display them.
* (3.82) Small bug cleared to allow Customizer access to form if access code in play
* (3.81) Special feature for [GCC CTLE](https://gccazctle.com/) If using normal posts in a SPLOTbox, the display of a single item will swap in name of WordPress author and link to archive
* (3.8) Support added for Loom, even more easy peasy to do
* (3.7) Support added for Vocaroo, easy peasy to do
* (3.6) Tags suggestion on the sharing form now works
* (3.5) Customizer options to modify the comment title and add a site specific prompt
* (3.4) Fields added for alternative text in images plus media descriptions that can link t transcripts for audio/video
* (3.32) Added support for Audioboom 
* (3.2) Counts added for all archive results, fixed issue with license links
* (3.0) HTML5 Audio recorder support 
* (2.2) Admin options to select all / none for media sites, fixed bug where drafts were visible on front page
* (2.1) Better formatting for Internet Archive audio, and new checking for supported media types from the archive, added missing Customizer editing for all form labels, made new admin bar link to open Customizer to Sharing Form
* (2.0) A big revamp, removing the need for special user accounts and secret logins. No logins are used to add content. The theme us much simpler, there is no special desk page for routing the access code. And the sharing form now has radio buttons that toggle the two different means of upload (by URL or by Upload)
* (1.9) Support included now for Giphy, Mixcloud, Flickr, Speakerdeck, and Slideshare, plus uploads now include ability to add images. The theme options can be used to limit the sites enabled. Also, the theme no longer needs a separate page for the random link generator (it's done all with code).
* (1.6-1.8) ! Yikes, someone forgot to list the new features. We are sure they were awesome.
* (1.5) Previews of submissions added to share form (displays as lightbox overlay). Pagination fixed for view by license template, shortcode added for license. Changed post format to not use content above more tag to find media URLs (Gunteberg comment tag danger), but reference media url stored in custom field (older formats still work). Tested to work with WordPress 5, Gutenberg, and newest version of parent theme. And, because the file got so long, `functions.php` has now been divided up into component files stored inside `/includes/'
* (1.0) Support added to allow submission of URLs for Adobe Spark Pages and Videos. Options added to hide form inputs for categories and tags if not needed. Fixed theme to use parent theme fonts and icons.
* (0.6) Template added to display media by type of open license applied
* (0.4) Support for Internet Archive audio and video embed from URL. Page genertor of links to all content with the same reuse license
* (0.3) Edit labels and instructions on the upload form using the Wordpress Customizer
* (0.2) A rich text editor can now be enabled for the description fields or you can opt to use the simpler plain text input text area for descriptions.
* (0.15) On new installs where no menus are defined, the theme generates a simple menu rather than listing all pages 




