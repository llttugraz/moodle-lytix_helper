# lytix_helper

## Overview
The `lytix_helper` plugin acts as a central repository for all auxiliary classes and functionalities used across various modules in the Moodle system. It streamlines various operations, from calculations to form handling, ensuring a consistent and efficient approach throughout the system.

## Features

### Core Constants and Calculation Functions
- Houses vital constants and key mathematical functions, such as computing the median, mean, etc., found in `calculation_helper.php`.

### Modular Design Choice
- The decision to externalize functionalities into a separate module ensures a leaner and more efficient core module.

### Support for Automated Unit Tests
- Contains numerous utility functions that aid in automated unit testing.

### Aggregation of User Activities
- Functions dedicated to summarizing user activities within a course can be found in `aggregate_user_activities.php`.

### Course Settings Management
- Provides functions related to course settings, offering capabilities to retrieve or modify these settings, as seen in `course_settings.php`.

### Form Handling
- Comes equipped with functionalities related to form handling in Moodle, encapsulated within `forms_helper.php`.

### Type Definitions
- The `types.php` file encompasses various type definitions or constants utilized in other parts of the plugin.

## Usage
1. Install and activate the `lytix_helper` module in your Moodle instance.
2. Other modules can now seamlessly access the helper functions and constants provided by this plugin.
