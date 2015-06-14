# Transit #

*Documentation may be outdated or incomplete as some URLs may no longer exist.*

A lightweight file uploader that also provides extended support for file validation, file transformation (image resizing, cropping, etc) and file transportation (moving files to Amazon S3 or another external storage system).

* Easily upload a file into the local file system
* Basic support for file moving and renaming
* Overwrite protection and file name filtering
* Import a file from a remote location, local file system path or an input stream
* Transform and alter a file by running a Transformer on it
* Create new files based off an original file by using Transformers
* Transport to or delete a file from Amazon S3 or Glacier
* Validate files and images using a defined set of rules
* Support for extending built in Transporters, Transformers and Validators