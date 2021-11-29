# Developer Support - Troubleshooting Exercise

Put yourself in the shoes of a Slack developer. You're building an app but it's not working as expected and now you need to debug your code.

## End goal

The app that you're building makes use of Slack's Events API. It listens to the `user_change` event for a user changing status, and then posts a message containing their custom status to the #statuses channel in their Slack workspace.

The final design should look like this:

![final result](Final%20Result.png)

## Files

There are a handful of small coding errors in the provided `index.php` file. You should only need to edit the contents of `index.php` to get the app working.

Please document the changes you make, along with your reasoning behind making these changes. 

## Configuring the Slack App

1. Create a new Slack workspace for testing: https://slack.com/create
2. In that workspace, create a new public channel.
3. Right-click on the channel's name in the sidebar and copy the channel link. Paste the ID from that link (which looks like `C0123ABC`) and create a `CHANNEL` environment variable on your server (see https://stackoverflow.com/questions/19696230/how-to-set-global-environment-variables-for-php).
4. Create a new Slack app and assign it to your newly created workspace: https://api.slack.com/apps
5. Go to the app's *OAuth & Permission* page and add the `chat:write:bot` and `users:read` scopes. Save your changes.
6. At the top of that page, press the green *Install App to Workspace* button to install it on your new workspace.
7. After authorizing the app, you'll see an *OAuth Access Token* that starts with `xoxp`. Copy that token and create a `TOKEN` environment variable on your server.
10. Ensure your PHP server is running, and accessible to the Internet. 
11. Go to the app's *Event Subscriptions* page and enable the feature.
12. Copy your app's server URL. Paste that full URL as the *Request URL*.
13. Subscribe to the `user_change` event and save the settings.

## Running/restarting the app

The test exercise is a simple PHP file with the following dependencies:

* PHP 5.x or higher
* Curl

To test this, you will need to create a free Heroku account, and configure a PHP server as well as a new app on your workspace. You can learn more about this here: https://devcenter.heroku.com/articles/getting-started-with-php and here: 

Optionally, if you have an existing PHP hosting account, you can host this code there. 

When setting up your app, the `index.php` provided in this archive should be used for the Request URL on the Event Subscriptions tab in your app's configuration, and you should subscribe to the `user_change` event. You will also need to add the `channels:write` scope to your app. 

### Triggering `user_change` events

As long as the server is running, change your Slack status to something new in order for an event to be triggered.

## How to Submit this exercise

Post this code to a new repository in Github, then start making your changes. **Please keep a written log** of the steps you take to debug, then resolve each fix. Ideally, commit to the repository after each fix so we can see how you've approached the problem. 

When you have a working app, please submit your written log and a link to your Github repository back to Meagan.

## Links to help you out
* [Building Slack apps](https://api.slack.com/slack-apps)
* [Events API](https://api.slack.com/events-api)
* [`user_change` event](https://api.slack.com/events/user_change)
