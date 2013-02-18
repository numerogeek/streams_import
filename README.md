# Streams Import Module
==============

An awesome module to help you to import data into Streams for PyroCMS.

## Installation

Download and install the module through the Admin or Manually

## How to use

### Profiles

Profiles can be used to setup a common import you will run multiple times. A Profile will store your destination Stream and field mapping to eliminate these steps in the future.

#### Run an import.

If you have not created a profile, do so now. A Profile will contain all of the "config" type info for future imports.

Once your profile is created, you can click on __Run__ which will propose you to select a File you've uploaded in Files Module, and will import it instantly.

#### Customization in mind

Sometimes you will wish to handle a specific fields and preprocess it before running the insert. So the module create a for you 2 helpers.
The first one is for preprocessing data. All the fields of the stream you want import into are listed here. 
the second one is for post processing data. A unique function with the $data and $entry_id to make whatever you want.

### Tehcnical detail

The module will try to convert your file into a PHP array. 
If for some reason, the root of the array is not the items you want to loop in, you can customize it in the Files settings of the profiles.
You'll need to check the rights for the directory : helpers/profiles. 

## Improve it ! 

Feel free to improve this Free and awesome module :)
It's in beta for now, but we all need import system in our favorite CMS !

## Issues

Please submit any issues back to the Github issue tracker: <https://github.com/bergeo-fr/streams_import>
