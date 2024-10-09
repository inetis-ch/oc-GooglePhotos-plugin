# Plugin deprecation


> [!WARNING]  
> [Updates in the google photos API](https://developers.google.com/photos/support/updates) taking effect march 2025 will break the basic functionality of this plugin.  
>We will no longer be supporting it and recommend finding an alternative.  

# About
OctoberCMS plugin for displaying a gallery from Google Photos (Picasa) through the use of a CMS component or a RainLab.Pages snippet.

<img src="https://user-images.githubusercontent.com/16371551/28569844-dc3f11ce-713b-11e7-8ce6-24e2cae156b2.gif">

## Prerequisites
This plugin comes with an OAuth client application that will only work on `localhost` to allow you to test the plugin.

Before deploying it on any hostname other than `localhost`, you must create your own OAuth app credentials or use existing credentials for the hostname in question (i.e. `example.com`). Follow these steps to get your credentials: [Google documentation](https://support.google.com/cloud/answer/6158849).
The documentation will say that "the Authorized redirect URI does not require a value", but it is required for this use case. You will need to set the Authorized redirect URI to `https://example.com/backend/inetis/googlephotos/oauth/callback`, replacing `example.com/backend` with your domain name and backend URL.

When done, you will be given a `Client ID` and a `Client Secret`, which you will need to provide to the plugin by overriding the configuration file. [See the official documentation](https://octobercms.com/docs/plugin/settings#file-configuration) on doing this. Basically, just copy the file `/plugins/inetis/googlephotos/config/config.php` to `/config/inetis/googlephotos/config.php` and put your app credentials inside.

Finally, you need to enable enable the [Photos Library API](https://console.cloud.google.com/apis/library/photoslibrary.googleapis.com) for your project.

## Installation
* Add the component to a CMS page
* Login to your Google account from the plugin settings. If you get a 404 when clicking on the link, you have missed something while setting up your OAuth app

## Setup
You need to create two CMS pages

### One to display the albums of a single gallery

For this one you need an additional `:albumId` routing parameter in the URL of the page.
Add the **Google Photos album** component and in the component settings set the `Album ID` property to the name of the routing parameter you setup for this page (i.e. `:albumID`).

### One to display all galleries

Add the **Google Photos albums list** component to this page, setting the `Album Page` parameter to the page you created for the albums of a single gallery.

## Additional configuration

### Ignored albums
By default, Google Photos shows all albums related to your Google account or Google+ profile including automatically generated ones like "Auto Backup" and "Profile Photos". You can hide these albums from the plugin settings by adding as many albums as you want to the "Hidden albums" section. Just click on "Add new item" and fill the field with either an album ID or an album name.

## Author
inetis is a webdesign agency in Vufflens-la-Ville, Switzerland. We love coding and creating powerful apps and sites  [see our website](https://inetis.ch).
