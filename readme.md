# Warm Transfer: Transfer support calls from one agent to another using PHP and Laravel
[![Build Status](https://travis-ci.org/TwilioDevEd/warm-transfer-laravel.svg)](https://travis-ci.org/TwilioDevEd/warm-transfer-laravel)

## Local development

This project is built using the [Laravel](https://laravel.com/) web framework.

1. Clone the repository and `cd` into it.

1. Install the application's dependencies with [Composer](https://getcomposer.org/).

   ```bash
   $ composer install
   ```

1. This application uses PostgreSQL as the persistence layer. You should install
  it if you don't have it. The easiest way is by
  using [Postgres.app](http://postgresapp.com/).

1. Create a database.

  ```bash
  $ createdb warm_transfer
  ```

1. Copy the sample configuration file and edit it to match your configuration.

   ```bash
   $ cp .env.example .env
   ```

  You can find your `TWILIO_ACCOUNT_SID` and `TWILIO_AUTH_TOKEN` under
  your
  [Twilio Account Settings](https://www.twilio.com/user/account/settings).
  You can buy a Twilio phone number here [Twilio numbers](https://www.twilio.com/user/account/phone-numbers/search).
  `TWILIO_NUMBER` should be set according to the phone number you purchased above.

1. Generate an `APP_KEY`.

   ```bash
   $ php artisan key:generate
   ```

1. Run the migrations.

  ```bash
  $ php artisan migrate
  ```

1. Expose your application to the wider internet using [ngrok](http://ngrok.com). This step
  is important because the application won't work as expected if you run it through
  localhost.

  ```bash
  $ ngrok http 3000
  ```

  Once ngrok is running, open up your browser and go to your ngrok URL. It will
  look something like this:

  `http://9a159ccf.ngrok.io`

  You can read [this blog post](https://www.twilio.com/blog/2015/09/6-awesome-reasons-to-use-ngrok-when-testing-webhooks.html)
  for more details on how to use ngrok.

1. Configure Twilio to call your webhooks.

  You will also need to configure Twilio to call your application when calls are received on your `TWILIO_NUMBER`. The voice URL should look something like this:

  ```
  http://9a159ccf.ngrok.io/conference/connect/client
  ```

  [Learn how to configure your Twilio phone number for voice calls](https://www.twilio.com/docs/voice/tutorials/warm-transfer-php-laravel#set-up-the-voice-web-hook)

1. Run the application using Artisan.

  ```bash
  $ php artisan serve
  ```

  It is `artisan serve` default behavior to use `http://localhost:8000` when
  the application is run. This means that the IP address where your app will be
  reachable on you local machine will vary depending on the operating system.

  The most common scenario is that your app will be reachable through address
  `http://127.0.0.1:8000`. This is important because ngrok creates the
  tunnel using only that address. So, if `http://127.0.0.1:8000` is not reachable
  in your local machine when you run the app, you must tell artisan to use this
  address. Here's how to set that up:

  ```bash
  $ php artisan serve --host=127.0.0.1
  ```
That's it!


## How to Demo

1. Navigate to `https://<ngrok_subdomain>.ngrok.io` in two different
   browser tabs or windows.

   **Notes:**
   * Remember to use your SSL enabled ngrok URL `https`.
   Failing to do this won't allow you to receive incoming calls.

   * The application has been tested with [Chrome](https://www.google.com/chrome/)
   and [Firefox](https://firefox.com). Safari is not supported at the moment.

1. In one window/tab click `Connect as Agent 1` and in the other one click
   `Connect as Agent 2`. Now both agents are waiting for an incoming call.

1. Dial your [Twilio Number](https://www.twilio.com/user/account/phone-numbers/incoming)
   to start a call with `Agent 1`. Your `TWILIO_NUMBER`
   environment variable was set when configuring the application to run.

1. When `Agent 1` answers the call from the client, he/she can dial `Agent 2` in
   by clicking on the `Dial agent 2 in` button.

1. Once `Agent 2` answers the call all three participants will have joined the same
   call. After that, `Agent 1` can drop the call and leave both the client and `Agent 2`
   having a pleasant talk.

## Meta

* No warranty expressed or implied. Software is as is. Diggity.
* [MIT License](http://www.opensource.org/licenses/mit-license.html)
* Lovingly crafted by Twilio Developer Education.
