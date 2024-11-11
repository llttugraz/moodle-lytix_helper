### v2.0.4 - 25.10.2024
- Update for compatibility with Moodle 4.5
- Fix dependencies
- Adapt README.md
- Provide CHANGES.md

### v2.0.3 - 21.05.2024
v2.0.1 was compatible with Moodle 4.1 but not with Moodle 4.2, v2.0.2 did not reflect the changed version in version.php; this release fixes these mistakes and also contains previously introduced changes:

- Add translation for several languages

### v1.1.6 - 21.05.2024
v1.1.5 did not reflect the changed version in version.php and composer.json; this release fixes this mistake and also contains previously introduced changes:

- Add translation for several languages

### v1.1.4 - 28.11.2023
- Fix to add COMPLETION_ENABLED to courses in tests
- PhpDoc improvements

### v2.0.0 - 22.11.2023
- This release introduces all versions for Moodle 4.2 and later

### v1.1.3 - 21.11.2023
- This tag introduces the version-branch for Moodle 4.1

### v1.1.2 - 07.11.2023
- Calculation for lytix_timeoverview and lytix_activity is now done here
- Removed lytix_grademonitor
- Removed unused functions

### v1.1.1 - 30.10.2023
- Several minor changes for Moodle 4.1 and php 8.x
- Fixed a bug with the semester end with courses that do not have a fixed end
- Completly reworked the aggregation function for the pluigins lytix_acitivity and lytix_timeoverview
- The calculation is completly done in the Database and therefore much faster

### v1.1.0 - 04.10.2023
- Preparation for the next steps in november, refactoring of the code
