# Moodle Rocket.Chat Sync Plugin
The Rocket.Chat plugin is an integration between Moodle that allows users to push students from Moodle into channels on Rocket.Chat. These channels correspond to their groups in Moodle.

## Status
This was built quite quickly so we could test out how collaboration would work leveraging Rocket.Chat with Moodle. Please expect changes.

## Main features
1. Channel creation
2. User creation - role filter available
3. Channel subscription based on groups in Moodle - regex filter available
4. Manual sync
5. Background task sync
6. User activation/deactivation based on `user_enrolment_updated` moodle event

## Installation
1. Copy the Rocket.Chat directory to the local directory of your Moodle instance
2. Visit the notifications page

For more information, visit [documentation](http://docs.moodle.org/en/Installing_contributed_modules_or_plugins) for installing contributed modules and plugins.

*Note* - you need a running Rocket.Chat server that you can point the plugin to. If you aren't sure how to do this, checkout the [documentation](https://rocket.chat/docs/installation/) on Rocket.Chat. I also added a bit of [code](https://github.com/getsmarter/rocketchat-api-rest) to Rocket.Chat to make integration a little easier. This unfortunately will require a custom build of the Rocket.Chat source code. 

## Usage
### Settings
A new `Site Adminstration` section has been added under `Rocket.Chat`.  The `settings` page allows administrators to enter the host, username and password for Rocket.Chat. To get these settings you need to create a user on Rocket.Chat with whatever credentials you want and paste them into Moodle.

The regex block allows you to add group regex filters. When a sync between Moodle and Rocket.Chat happens, channels are created for each group that matches your regex expression. You can have multiple expressions on new lines. If you don't want any groups, don't add any regex expressions.

### Integration
There are two tables that help control what data is pushed to Rocket.Chat.

1. Roles Included in Sync - determines what user roles can be pushed to Rocket.Chat
2. Integrated Courses - allows setting of a course to sync in the background (task and cron based) or a manual sync that is executed immediately.

## Todo
- Add an activity/resource/block that can be added within a course. 
- Handle deletion/suspension of users in Moodle - right now the sync only pushes students to Rocket.Chat. It does not remove them.
- Improve error reporting one sync failure
- Improve error reporting for suspensions

