# Google Photos plugin for OctoberCMS
This plugin offers a component (also available as snippet) to display a gallery from Google Photos (Picasa)

<img src="https://user-images.githubusercontent.com/16371551/28569844-dc3f11ce-713b-11e7-8ce6-24e2cae156b2.gif">

# How to use it ?

## Installation
* Install the component
* Login to your Google account from the plugin settings
  <img src="https://monosnap.com/file/1CW6okRvNjjxBXkUQiBIcFfW0BdxCs.png">
  
## Setup
You need to create two CMS pages

### One to display the albums of a single gallery
<img src="https://monosnap.com/file/bOl4CzT6FjTGD7I4PwwvYpunSIMtdP.png">

For this one you need an additional "albumId" parameter.

On this page, include the `Google Photos album` component.
In the components settings, setup your `Album ID` parameter.

### One to display all galleries
<img src="https://monosnap.com/file/MrAmYQtbOzUGQspvsvTMa2gw3H7zQQ.png">

On this page, include the `Google Photos albums list` component.
In the components settings, set the `Album Page` parameter to the Page you just created before.

## Additional configuration

### Ignored albums
By default, Google Photos shows some albums related to your Google account or Google+ profile such as "Auto Backup" or "Profile Photos".
You can hide undesired albums from the settings of the plugin.
Here you can add as many ignored albums as you want: under "Hidden albums", click on "Add new item" and fill the new field with either an album ID or an album name.

### Custom OAuth app
Out of the box, this plugin comes with an OAuth app pre-configured. However there is no guarantee that this app won't be banned or deleted.
If you want to use your own OAuth app, you will first to obtain a `client_id` and `client_secret` (see https://console.developers.google.com).

Once you got these credentials, you can override the config of the plugin ([see official doc](https://octobercms.com/docs/plugin/settings#file-configuration):
Copy the file `/plugins/inetis/googlephotos/config/config.php` to `/config/inetis/googlephotos/config.php` and put your app credentials inside.

# The OAuth library (how to use it in other projects)
This plugin authenticates to Picasa Web Albums Data API with OAuth2 using a library that you can find inside the `picasawebdata/base` directory.
If you want to use this library in a non-October project, you will need to include the small October's HTTP client (`October\Rain\Network\Http`).

## The OAuth token class
The class `Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken` is used as a representation of an OAuth token.
The only methods you need in an OAuthToken object are:
- `getAccessToken()` returns a valid (refreshed if needed) Access token that you can use in your requests
- `revoke()` revoke the token

But where it becomes a bit more complicated is when instantiating this OAuthToken: you need to provide a "Settings Provider" and a "Stored Token".

## The Stored Token
You have to make a class that implements the `Inetis\GooglePhotos\PicasaWebData\Base\Tokens\StoredTokenInterface` interface.
This class is basically a container for a raw token that you will receive from an authentication server.
It's the role of this class to store and retrieve the raw token depending on how you want it to be stored in your project, and also to provide accessors for properties of the token such as "Access token", expiration, etc.

You can use the implementation for OctoberCMS as a base: `Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsStoredToken`.

## The Settings Provider
You have to make a class that extends the abstract class `Inetis\GooglePhotos\PicasaWebData\Base\Settings\BaseSettingsProvider`.
This class is used to provide diverse services, mainly dependent of project environment.

As it is, the BaseSettingsProvider class is made to work with Google OAuth APIs, but you can override some properties and methods to suit your OAuth server's needs.

Have a look at the implementation for October at `Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsProvider` to better understand what this class exactly does.

In some cases (depending on your project needs), your StoredToken implementation doesn't need any arguments in its constructor. In such cases, the StoredToken can be instantiated automatically using the SettingsProvider by some classes of the library.
For example, when instantiating the OAuthToken, you can do this:
```PHP
// Normally the OAuthToken needs a SettingsProvider and a StoredToken
$token = new OAuthToken(new MySettingsProvider(), new MyStoredToken());

// But as "MyStoredToken" can be instantiated without arguments, you can do this instead
$token = new OAuthToken(new MySettingsProvider());
```

## Finally
After having written these adapters for your environment, you can use the token like this:
```PHP
////////// User Authentication //////////

// Ask user authorization by getting his browser to this url
$settingsProvider = new MySettingsProvider();
print('<a href="'. $settingsProvider->buildAuthUrl() .'">Login</a>');

// Somewhere, listen to the callback route you have defined in MySettingsProvider
function onReceiveResponse()
{
    $settingsProvider = new MySettingsProvider();
    $settingsProvider->exchangeToken($_GET['code']);
}

////////////// Token usage //////////////

// If the getStoredToken() method of MySettingsProvider don't need arguments, you don't need to instantiate a StoredToken yourself
$oAuth = new OAuthToken(new MySettingsProvider());

// Get a valid access token
$accessToken = $oAuth->getAccessToken();

// Issue a request to your authenticated API
use October\Rain\Network\Http;
$response = Http::make('api.example.com/users/me', 'GET')
    ->header('Authorization', 'Bearer ' . $accessToken)
    ->send();
    
/////////// Token revocation ////////////

// If you want to invalidate the token, you can do it like this
$oAuth->revoke();
```
