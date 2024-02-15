# lytix\_helper

This plugin is a subplugin of [local_lytix](https://github.com/llttugraz/moodle-local_lytix).
The `lytix_helper` plugin acts as a central repository for all auxiliary classes and functionalities used across various modules in the Moodle system. It streamlines various operations, from calculations to form handling, ensuring a consistent and efficient approach throughout the system.

## Table of Contents

- [Installation](#installation)
- [Requirements](#requirements)
- [Features](#features)
- [Configuration](#configuration)
- [Usage](#usage)
- [Dependencies](#dependencies)
- [API Documentation](#api-documentation)
- [Subplugins](#subplugins)
- [Privacy](#privacy)
- [FAQ](#faq)
- [Known Issues](#known-issues)
- [Changelog](#changelog)
- [License](#license)
- [Contributors](#contributors)

## Installation

1. Download the plugin and extract the files.
2. Move the extracted folder to your `moodle/local/lytix/modules` directory.
3. Log in as an admin in Moodle and navigate to `Site Administration > Plugins > Install plugins`.
4. Follow the on-screen instructions to complete the installation.

## Requirements

- Moodle Version: 4.1
- PHP Version: 7.4, 8.0, 8.1
- Supported Databases: MariaDB, PostgreSQL
- Supported Moodle Themes: Boost

## Features

- Houses vital constants and key mathematical functions, such as computing the median, mean, etc., found in `calculation_helper.php`.
- The decision to externalize functionalities into a separate module ensures a leaner and more efficient core module.
- Contains numerous utility functions that aid in automated unit testing.
- Functions dedicated to summarizing user activities within a course can be found in `aggregate_user_activities.php`.
- Provides functions related to course settings, offering capabilities to retrieve or modify these settings, as seen in `course_settings.php`.
- Comes equipped with functionalities related to form handling in Moodle, encapsulated within `forms_helper.php`.
- The `types.php` file encompasses various type definitions or constants utilized in other parts of the plugin.

## Configuration

Describe here how to configure the plugin after installation, including available settings and how to adjust them.

## Usage

Explain how to use the plugin with step-by-step instructions and provide screenshots if they help clarify the process.

## Dependencies

- [local_lytix](https://github.com/llttugraz/moodle-local_lytix).
- [lytix_config](https://github.com/llttugraz/moodle-lytix_config).
- [lytix_logs](https://github.com/llttugraz/moodle-lytix_logs).

## API Documentation

If your plugin offers API endpoints, document what they return and how to use them here.

## Subplugins

If there are any subplugins, provide links and descriptions for each.

## Privacy

Detail what personal data the plugin stores and how it handles this data.

## FAQ

**Q:** Frequently asked question here?
**A:** Answer to the question here.

**Q:** Another frequently asked question?
**A:** Answer to the question here.

## Known Issues

- Issue 1: Solution or workaround for the issue.
- Issue 2: Solution or workaround for the issue.

## License

This plugin is licensed under the [GNU GPL v3](https://github.com/llttugraz/moodle-lytix_helper?tab=GPL-3.0-1-ov-file).

## Contributors

- **Günther Moser** - Developer - [GitHub](https://github.com/ghinta)
- **Alex Kremser** - Developer - [GitHub](https://github.com/llt-tuggy)

