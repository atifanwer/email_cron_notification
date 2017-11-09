# Email Cron Notification

Notifies the users of a given role each time the Drupal cron runs.

## Description

The Email Cron Notification module provides a configuration interface 
for defining one or more roles who shall receive a notification each time 
Drupal cron runs.

## Benefits

Be notified on cron tasks.

## Notices

In development, left improvement todo :
* Provide means for setting which info shall be sent, e.g. info about the cron result and specific cron jobs 
(token replacement in mail body, dedicated setting fields ?)
* else ?


## Installing Email Cron Notification
1. Install the module as normal, note that there are X dependencies.
2. Configure the module at admin/config/system/cron/notifications - add a new group by creating by clicking "Add Notification".
3. Fill out the form and save it, optionnaly adding some specific email outside the defined role
4. When you save new notification from now on, it should automatically send the according notification each time the cron runs.

## Credits:

This module is based on the Content Moderation Notification by [Jonathan Hedstrom](https://www.drupal.org/u/jhedstrom) and [Rob Holmes](https://www.drupal.org/u/rob-holmes)

Current maintainers:

- Benoît de Raemy
