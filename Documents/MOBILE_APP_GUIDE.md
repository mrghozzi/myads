# MYADS Mobile App Guide

The official mobile app for the MYADS platform is available as an open-source Flutter project. 

**Repository Link:** [https://github.com/mrghozzi/myads_app](https://github.com/mrghozzi/myads_app)

This guide explains how to connect the Flutter mobile app to your MYADS website installation.

## Requirements

1. A running MYADS website (v4.3.4 or higher).
2. The MYADS mobile app codebase cloned from the repository above.
3. Flutter SDK (v3.27+) and Android SDK installed on your development machine.

## How to Connect the App to the Website

To connect the mobile app to your website, you need to configure the API base URL and the API Key in the app's environment file.

### 1. Generate an API Key

First, you need to generate a secure API Key from your MYADS website to authorize the mobile app.

1. Log in to your MYADS admin panel as a Super Admin.
2. Navigate to the **API Settings** (or generate it securely according to your admin panel instructions).
3. Copy the generated API Key.

### 2. Configure the App Environment Variables

In the root directory of the `myads_app` Flutter project, you will find an example environment file named `.env.example`.

1. Copy `.env.example` and rename it to `.env` in the same directory.
2. Open the `.env` file in your code editor.
3. Set the `BASE_URL` to your MYADS website's API endpoint. It must end with `/api`.
4. Set the `API_KEY` to the key you generated in the previous step.

Example `.env` configuration:
```env
BASE_URL=https://your-myads-site.com/api
API_KEY=your_generated_api_key_here
```

### 3. Run the App

After configuring the `.env` file, install the Flutter dependencies and run the app:

```bash
flutter pub get
flutter run
```

The app will now connect to your MYADS website, allowing users to log in, view the community feed, interact with posts, and use all the available mobile features.
