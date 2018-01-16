# About
OctoberCMS plugin for displaying a gallery from Google Photos (Picasa) through the use of a CMS component or a RainLab.Pages snippet.

<img src="https://user-images.githubusercontent.com/16371551/28569844-dc3f11ce-713b-11e7-8ce6-24e2cae156b2.gif">

## Prerequisites
This plugin comes with an OAuth client application that will only work on `localhost` to allow you to test the plugin.

Before deploying it on any hostname other than `localhost`, you must create your own OAuth app credentials or use existing credentials for the hostname in question (i.e. `example.com`). Follow these steps to get your credentials: [Google documentation](https://developers.google.com/identity/sign-in/web/devconsole-project). 
The documentation will say that "the Authorized redirect URI does not require a value", but it is required for this use case. You will need to set the Authorized redirect URI to `https://example.com/backend/inetis/googlephotos/oauth/callback`, replacing `example.com/backend` with your domain name and backend URL.

When done, you will be given a `Client ID` and a `Client Secret`, which you will need to provide to the plugin by overriding the configuration file. [See the official documentation](https://octobercms.com/docs/plugin/settings#file-configuration) on doing this. Basically, just copy the file `/plugins/inetis/googlephotos/config/config.php` to `/config/inetis/googlephotos/config.php` and put your app credentials inside.

## Installation
* Add the component to a CMS page
* Login to your Google account from the plugin settings. If you get a 404 when clicking on the link, you have missed something while setting up your OAuth app
  <img src="https://monosnap.com/file/1CW6okRvNjjxBXkUQiBIcFfW0BdxCs.png">
  
## Setup
You need to create two CMS pages

### One to display the albums of a single gallery
<img src="https://monosnap.com/file/bOl4CzT6FjTGD7I4PwwvYpunSIMtdP.png">

For this one you need an additional `:albumId` routing parameter in the URL of the page. 
Add the **Google Photos album** component and in the component settings set the `Album ID` property to the name of the routing parameter you setup for this page (i.e. `:albumID`).

### One to display all galleries
<img src="https://monosnap.com/file/MrAmYQtbOzUGQspvsvTMa2gw3H7zQQ.png">

Add the **Google Photos albums list** component to this page, setting the `Album Page` parameter to the page you created for the albums of a single gallery.

## Additional configuration

### Ignored albums
By default, Google Photos shows all albums related to your Google account or Google+ profile including automatically generated ones like "Auto Backup" and "Profile Photos". You can hide these albums from the plugin settings by adding as many albums as you want to the "Hidden albums" section. Just click on "Add new item" and fill the field with either an album ID or an album name.

## Advanced Usage of the included OAuth Library in other projects
This plugin authenticates with the Picasa Web Albums Data API via OAuth2 using a library located in the `picasawebdata/base` directory.
If you want to use this library in a non-October project, you will need to include the OctoberCMS HTTP client (`October\Rain\Network\Http`).

### OAuth token class
The class `Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken` is used as a representation of an OAuth token. The only methods you need in an OAuthToken object are:
- `getAccessToken()` returns a valid (refreshed if needed) access token that you can use in your requests
- `revoke()` revokes the token

When instantiating this OAuthToken a **Stored Token** and a **Settings Provider** are both required.

#### Stored Token
A Stored Token is a class that implements the `Inetis\GooglePhotos\PicasaWebData\Base\Tokens\StoredTokenInterface` interface.
This class is basically a container for a raw token that is received from an authentication server. The role of this class is to store and retrieve the raw token depending on how it is to be stored in the project and also to provide acccessors for properties of the token (such as `Access token`, `expiration`, etc).

See `Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsStoredToken` for more information.

#### Settings Provider
A Settings Provider is a class that extends the abstract class `Inetis\GooglePhotos\PicasaWebData\Base\Settings\BaseSettingsProvider`.
This class is used to provide multiple services, mainly dependent on the project environment.

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

#### Finally
After having written these adapters for your environment, you can use the token like this:
```PHP
////////// User Authentication //////////
// Request user authorization by providing this URL to their browser
$settingsProvider = new MySettingsProvider();
header('Location: ' . $settingsProvider->buildAuthUrl());

// Listen to the callback route defined in MySettingsProvider
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

// In order to revoke the token, use the revoke() method on the OAuthToken object
$oAuth->revoke();
```

## Author
inetis is a webdesign agency in Vufflens-la-Ville, Switzerland. We love coding and creating powerful apps and sites  [see our website](https://inetis.ch).
