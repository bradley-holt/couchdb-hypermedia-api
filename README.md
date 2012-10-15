# CouchDB Hypermedia API

[![Build Status](https://secure.travis-ci.org/bradley-holt/couchdb-hypermedia-api.png)](http://travis-ci.org/bradley-holt/couchdb-hypermedia-api)

This is the source code for a proof of concept Hypermedia API built in CouchDB.

## License

This application is licensed under the New BSD License.

## Developing

Development of this application is done using [VirtualBox](https://www.virtualbox.org/) 
and [Vagrant](http://vagrantup.com/). To set up your own local development environment:

1. Install [VirtualBox](https://www.virtualbox.org/)
2. Install [Vagrant](http://vagrantup.com/)
3. Add the Ubuntu 12.04.1 LTS x86_64 (Guest Additions 4.1.18) box (if not already on your system):  
`$ vagrant box add ubuntu-12_04-x86_64 http://dl.dropbox.com/u/1537815/precise64.box`
4. Bring up the virtual machine (from the root of this project):  
`$ vagrant up`
5. SSH into the virtual machine:  
`$ vagrant ssh`
6. Change into the `/vagrant` directory:  
`$ cd /vagrant`

## Building

This application's dependencies are managed by [Composer](http://getcomposer.org/). 
Run the following command to install dependencies:  
`$ ./composer.phar install --dev`

## Testing

The unit tests for this application can be run using the following command:  
`$ ./vendor/bin/phpunit`

## Administering

Administrative and maintainance tasks are available using the `admin.php` script. 
Executing this script will provide a list of available commands:  
`$ ./admin.php`
