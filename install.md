# Installing The SPLOTbox Theme

Here are your instructions for installing the [SPLOTbox WordPress theme](https://github.com/cogdog/splotbox).

Using this theme requires a self-hosted--or institutionally hosted (lucky you)-- Wordpress site (the kind that you download from [wordpress.org](http://www.wordpress.org). You cannot use this theme on the free "wordpress.com" site unless you have a business plan. Maybe check out [Reclaim Hosting](https://reclaimhosting.com/) if you need to set up your own hosting space. They are awesome.

SPLOTbox is a child theme based on [the free and elegant Garfunkel theme by Anders Noren](https://wordpress.org/themes/garfunkel). Install this theme first from within the Wordpress Dashboard under **Appearance** -- **Themes** searching on `Garfunkel`.

### Installing SPLOTbox From Scratch

You can [download a .zip file of this theme](https://github.com/cogdog/splotbox/archive/refs/heads/master.zip) via the green **Code*" button above. 

The zip can be uploaded directly to your site via **Themes** in the Wordpress dashboard, then **Add Theme** and finally **Upload Theme**. If you run into size upload limits or just prefer going old school like me, unzip the package and ftp the entire folder into your `wp-content/themes` directory.

You can update the theme at any time by uploading a newer version through the same steps above (check the [theme page](https://github.com/cogdog/splotbox) for the current version).

### Installing SPLOTbox in One Click with WP Pusher (get automatic updates!)

To have your site stay up to date automatically, I recommend trying the [WP Pusher plugin](https://wppusher.com/) which makes it easier to install themes and plugins that are published in GitHub. It takes a few steps to set up, but it's a thing of beauty when done.

To use WP-Pusher you will need to have or create an account on [GitHub](https://github.com/) (free). Log in. 

Next [download WP Pusher plugin](https://wppusher.com/download) as a ZIP file. From the plugins area of your Wordpress dashboard, click the **Upload Plugin** button, select that zip file to upload, and activate the plugin.

Then click the **WP Pusher** option in your Wordpress Dashboard, and then click the **GitHub** tab. Next click the **Obtain a GitHub Token** button to get an authentication token. Copy the one that is generated, paste into the field for it, and finally, click **Save GitHub** Token.

Now you are ready to install SPLOTbox! 

![](images/wp-pusher.jpg "WP Pusher Settings")

Look under **WP Pusher** for **Install Theme**. In the form that appears, under **Theme Repository**, enter `cogdog/truwriter`. Also check the option for **Push-to-Deploy** (this will automatically update your site when the theme is updated) finally, click **Install Theme**.

Wow, amazing, eh?

Not only does this install the theme without any messy download/uploads, each time I update the theme on GitHub, your site will be automatically updated to the newest version. 


## After the Install

To get the SPLOTbox  active on your site all you need to do is activate the "SPLOTbox" theme when it appears in the Wordpress dashboard under **Appearance** --> **Themes**.  Now you can move on to learn about setting it up in the [SPLOTbox Documentation](https://github.com/cogdog/splotbox/docs.md).


## Inserting Demo Content

If you want a site that is not completely empty, after setting up with WP-Pusher or from scratch, you can import all the content set up on the [public demo site](https://splot.ca/box). 

Install all content by [downloading the WordPress export for that site](https://github.com/cogdog/splotbox/blob/master/data/splotbox.xml).  Running the WordPress Importer (under **Tools** -- **Import**) and upload that file when prompted.

You can also get a copy of the Widgets used on that site too. First intall/activate the [Widget Importer & Exporter plugin](https://wordpress.org/plugins/widget-importer-exporter/). Download the [Writer Widgets data file](https://github.com/cogdog/splotbox/blob/master/data/writer-widgets.wie). Look under the **Tools** menu for **Widget Importer & Exporter** and use the Import Widgets section to upload the data file. Boom! You got my widgets.
