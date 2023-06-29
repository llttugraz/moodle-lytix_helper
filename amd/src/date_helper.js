/**
 * This counts the days between two dates, grouped by month, and gives a list of the month names.
 * The function has been designed for usage in visual timelines.
 *
 * @param {string|number|Date} startDate  The first day (can be anything that Date() accepts).
 * @param {string|number|Date} endDate  The last day (can be anything that Date() accepts).
 * @param {Intl.DateTimeFormat} formatter  The formatter to format month names.
 * @return {Object}  An object with two arrays: daysPerMonth & monthNames.
 */
export const getDaysPerMonth = (startDate, endDate, formatter) => {
    // These represent the first/last day of the CURRENTLY PROCESSED MONTH!
    let
        firstDay = new Date(startDate),
        lastDay = new Date(firstDay.setHours(0, 0, 0)); // Proper definition happens later.
    const
        finalDay = new Date(endDate),
        daysPerMonth = [],
        monthNames = [];

    do { /* eslint no-unmodified-loop-condition: 0 */
        // Actually set to last day of currently processed month.
        lastDay.setFullYear(firstDay.getFullYear());
        lastDay.setMonth(firstDay.getMonth() + 1, 1);
        lastDay.setDate(0);

        monthNames.push(formatter.format(lastDay));

        // Calculate and push difference.
        daysPerMonth.push(lastDay.getDate() - firstDay.getDate() + 1);

        // Advance to next month.
        firstDay.setTime(lastDay);
        firstDay.setDate(firstDay.getDate() + 1);
    }
    while (firstDay < finalDay);
    daysPerMonth[daysPerMonth.length - 1] = finalDay.getDate();

    return {daysPerMonth, monthNames};
};
