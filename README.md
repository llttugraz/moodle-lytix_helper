# lytix\_helper

This plugin is a subplugin of [local_lytix](https://github.com/llttugraz/moodle-local_lytix).
The `lytix_helper` plugin acts as a central repository for all auxiliary classes and functionalities used across various modules in the Moodle system. It streamlines various operations, from calculations to form handling, ensuring a consistent and efficient approach throughout the system.

## Versions

If you need **compatibility with Moodle 4.1** you should select releases below version 2.0.0, or checkout branch `moodle-4.1`.

## Installation

1. Choose the right version.
2. Download the plugin and extract the files.
3. Move the extracted folder to your `moodle/local/lytix/modules` directory.
4. Log in as an admin in Moodle and navigate to `Site Administration > Plugins > Install plugins`.
5. Follow the on-screen instructions to complete the installation.

## Requirements

- Moodle Version: 4.1+
- PHP Version: 7.4+
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

No settings for the subplugin are available.

## Usage

This module provides only backend functionalities.

## API Documentation

No API.

## Privacy

The following personal data of each user are stored if the functionality of LYTIX for a course is enabled:

| Entry            | Description                  |
|------------------|------------------------------|
| userid           | The ID of the user           |
| courseid         | The ID of the course         |
| bbb_click        | Click in BBB                 |
| bbb_time         | Time in BBB                  |
| contextid        | Contextid                    |
| core_click       | Clicks in Course             |
| core_time        | Time in Course               |
| feedback_click   | Clicks in Feedback           |
| feedback_time    | Time in Feedback             |
| forum_click      | Clicks in Forum              |
| forum_time       | Time in Forum                |
| grade_click      | Clicks in Gradebook          |
| grade_time       | Time in Gradebook            |
| h5p_click        | Clicks in h5p                |
| h5p_time         | Time in h5p                  |
| quiz_click       | Clicks in Quiz               |
| quiz_time        | Time in Quiz                 |
| resource_click   | Clicks in Resource           |
| resource_time    | Time in Resource             |
| submission_click | Clicks in Submission         |
| submission_time  | Time in Submission           |
| timestamp        | Timestamp                    |


## Dependencies

- [local_lytix](https://github.com/llttugraz/moodle-local_lytix)

## License

This plugin is licensed under the [GNU GPL v3](https://github.com/llttugraz/moodle-lytix_helper?tab=GPL-3.0-1-ov-file).

## Contributors

- **Günther Moser** - Developer - [GitHub](https://github.com/ghinta)
- **Alex Kremser** - Developer
