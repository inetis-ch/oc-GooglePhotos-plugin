# Update information

## Updating from 1.2.3 to 1.3.0
Version 1.3.0 is now using the GooglePhotos api instead of the PicasaWebData api.

There are a few changes that you should care about:
- You need to add the [Photos Library API](https://console.cloud.google.com/apis/library/photoslibrary.googleapis.com) to your Google Cloud Platform project
- It is no more possible to filter by albums visibility
- Album ids have changed. If you are using the `googlePhotosAlbum` component with a custom album, you need to select it again after updating
- If you have set some excluded albums by id, you should also change them
- The size definition for the thumbnails has changed. After updating the plugin, you will need to go trough everywhere you use the components to update the sizes.
- The photos urls returned by the API are now valid for 60 minutes only. Check that the cache duration in the plugin settings is no longer that 60 minutes.
- Some component properties names have changed (such as the photo title) in the API but the old equivalent are still available for backward compatibility.
- The `streams` array for videos no longer exists (it was not used in the default partial). Use `url` (or `photoUrl`) instead to get the video source.

If you have code based on `Inetis\GooglePhotos\PicasaWebData\PicasaClient`, there are some changes that you need to make:
- `Inetis\GooglePhotos\PicasaWebData\PicasaClient` have been removed, use `Inetis\GooglePhotos\PicasaWebData\GooglePhotosClient` instead.
- The second parameter (`$albumTitle`) of the `getAlbumImages()` method have been removed, use the new `getAlbum()` method to get this information.
