# Moodle Rocket.Chat Sync Plugin ![Moodle Plugin CI](https://github.com/adpe/moodle-local_rocketchat/workflows/Moodle%20Plugin%20CI/badge.svg)

The Rocket.Chat plugin is an integration between Moodle that allows users to push students from Moodle into channels on Rocket.Chat. These channels correspond to their groups in Moodle.

## Main features
1. Channel creation (private groups) for each Moodle group per course (Regex filter avaiable)
2. User creation in Rocket.Chat based on role in Moodle (Filter available)
3. Subscribe all users based on group in course
4. Manual sync: Trigger manually by clicking the button on `Course integration` page
5. Background task sync: Activate pending sync checkbox on `Course integration` page
6. User activation/deactivation based on `user_enrolment_updated` Moodle event
7. User subscription/desubscription based on `user_group_enrolment_updated` Moodle event

## Installation
1. Copy this Rocket.Chat plugin to the `local` directory of your Moodle instance: `git clone https://github.com/adpe/moodle-local_rocketchat.git local/rocketchat`
2. Visit the notifications page to complete the install process

For more information, visit [documentation](http://docs.moodle.org/en/Installing_contributed_modules_or_plugins) for installing contributed modules and plugins.

*Note* - you need a running Rocket.Chat server that you can point the plugin to. If you aren't sure how to do this, checkout the [documentation](https://rocket.chat/docs/installation/) on Rocket.Chat. I also added a bit of [code](https://github.com/getsmarter/rocketchat-api-rest) to Rocket.Chat to make integration a little easier. This unfortunately will require a custom build of the Rocket.Chat source code. 

## Usage
### Settings
A new `Site Adminstration` section has been added under `Rocket.Chat`.  The `settings` page allows administrators to enter the host, port, username and password for the Rocket.Chat connection. To get these settings you need to create a user on Rocket.Chat with whatever credentials you want and paste them into Moodle.

The regex block allows you to add group regex filters. When a sync between Moodle and Rocket.Chat happens, channels are created for each group that matches your regex expression. You can have multiple expressions on new lines. If you don't want any groups, don't add any regex expressions.

### Integration
There are two tables that help control what data is pushed to Rocket.Chat.

1. Roles Included in Sync: Determines what user roles can be pushed to Rocket.Chat
2. Integrated Courses: allows setting of a course to sync in the background (task and cron based) or a manual sync that is executed immediately.

## Todo
- Add an block that can be added within a course to access after the channels in a popup.
  - This block should also allow the creation of private chats with other users from the course.
- Handle deletion/suspension:
  - of users in Moodle: Right now the sync only pushes students to Rocket.Chat. It does not remove them.
  - of groups in Moodle: Right now the channels will not be removed on the Rocket.Chat server. Maybe we don't want that?!
- Improve error reporting:
  - on sync failure
  - for suspensions