# Changelog #

*These logs may be outdated or incomplete.*

## 1.5.1 ##

* Fixed an invalid range regex error during name parsing

## 1.5.0 ##

* Added a RackSpace CloudFiles transporter
* Added a base level file validator for easier extension

## 1.4.5 ##

* Includes changes from 1.4.4
* Add a unique ID to file names fix file overwrites
* Refactored `FitTransformer` so that images aren't expanded

## 1.4.3 ##

* Includes changes from 1.4.2
* Fixed aspect ratio scaling in `ResizeTransformer`
* Fixed rounding and scaling issues in all transformers [[#12](https://github.com/milesj/transit/issues/12)]
* Removed exception from `File::exif()` so that it doesn't interrupt the upload process

## 1.4.1 ##

* Fixed a bug where uploading images with masked or fake file extensions threw errors

## 1.4.0 ##

* Includes changes from previous minor versions
* Added `FitTransformer` to fit an image within a certain dimension while applying a background fill
* Added `Component` for all sub-classes to extend
* Added `File.supportsExif()` to check if the file has exif data
* Added a 2nd argument to `Transporter.transport()` that will override the default configuration
* Added an argument to `Transit.transport()` to pass configuration values to the transporter
* Updated `ExifTransformer` to only modify JPEG files
* Fixed bug where file renaming did not cache the new name or path
* Fixed bug with where exif reading would throw errors

## 1.3.2 ##

* Rename and add extension to files that do not have one

## 1.3.1 ##

* Swapped transformation order so that self transforms are triggered first

## 1.3.0 ##

* Added `File::exif()` to read Exif data from images
* Added Exif data to `File::toArray()`
* Added `ExifTransformer` to fix orientation and strip Exif data
* Added `RotateTransformer` to rotate images
* Added a `callback` option to image transformers
* Added `Transformer::getConfig()` and `setConfig()`

## 1.2.3 ##

* Added a 2nd argument to `Transit::importRemote()` that allows custom curl settings

## 1.2.2 ##

* Fixed a bug where renaming a file would not persist

## 1.2.1 ##

* Updated to extract mimetype from extension before `$_FILES`
* Updated to pass the full $_FILES array after `Transit::upload()`

## 1.2.0 ##

* Updated `File` to accept the `$_FILES` array through the constructor
* Updated `File::ext()`, `type()`, `name()`, and `basename()` to use the `$_FILES` data when available
* Updated `Transit` to use `$_FILES`

## 1.1.1 ##

* Added a `returnUrl` setting to `S3Transporter`
* Updated `File::type()` to extract the mimetype from the `file` command
* Updated `File::ext()` to extract the extension from the filename

## 1.1.0 ##

* Includes changes from 1.0.3 - 1.0.9
* Updated AWS SDK requirement to `2.2`
* Updated AWS S3 endpoint generation
* Updated `CropTransformer` `location` setting to accept an array
* Switched `Validator::mimeType()` and `type()`
* Fixed a bug where https URLs could not be imported
* Fixed a bug where remote imports fail when no filename is found
* Integrated Travis CI

## 1.0.2 ##

* Added getClient() to Aws/AbstractAwsTransporter
* Fixed S3 URLs not using the correct region endpoint

## 1.0.1 ##

* Added multibyte and curl checks to composer

## 1.0.0 ##

* Initial release of Transit
