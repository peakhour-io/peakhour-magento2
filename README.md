#Peakhour website acceleration for Magento 2

Congratulations on choosing to use Peakhour for your Magento 2 store. The Peakhour_Cdn extension
uses Magento's built in page caching functionality to list Peakhour.io as a full page cache option.
This enables greatly simplified full page caching with Peakhour and automated flushing of 
Peakhour's global cache when editing content through the Magento admin.

## Prerequisites

Before installing make a backup of your Magento site as a precaution. If you have a development
environment it is strongly recommended that you install the plugin there first to test and confirm
functionality.

You will need a Peakhour account to be able to utilise this plugin. If you don't already have one
you can [Sign up here](https://www.peakhour.io/app/signup/), pricing is based on page views and
all new signups get a free trial. Once you have your account the only other change you need to 
make to activate our acceleration and web application firewall is a DNS change. Do not enable the 
plugin until you have successfully configured your domain behind the Peakhour service.

## Install via Magento marketplace

This is only available for Magento 2.2.x and greater, see the [official magento doc page](https://docs.magento.com/m2/ce/user_guide/system/web-setup-extension-manager.html)

## Install via Composer

This will be done via command line on your Magento server. You will have to make sure [you have composer installed](https://getcomposer.org/download/). 

composer require peakhour/magento2
composer update
php bin/magento setup:upgrade
php bin/magento cache:clean
php bin/magento setup:di:compile

## Configuration

1. Log in to your Magento admin
2. Navigate to Stores -> Configuration, then Advanced -> System, then Full Page Cache
3. Untick Use System Value
4. Select Peakhour.io from the dropdown for Caching Application
5. Expand the Peakhour.io Settings section
6. Enter your Peakhour API Key:
6.1 Log in to your Peakhour account
6.2 Click on API Keys in the left menu.  
6.3 If you don't have a key for your domain then enter your domain name and click Create
6.4 Copy the generated key and paste into the API Key field in the Peakhour.io settings section
6.5 Enter your domain name and save config
6.6 Click 'Test Connection', you will hopefully see 'Success!'
