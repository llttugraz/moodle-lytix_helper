import Ajax from 'core/ajax';
import Log from 'core/log';
import * as Str from 'core/str';


class WidgetError extends Error {
    constructor(message, stringKey) {
        super(message);
        this.name = 'WidgetError';
        this.stringKey = stringKey;
    }
}

class NoDataError extends WidgetError {
    constructor() {
        super('Not enough data available.', 'no_data_available');
    }
}


const moodleComponent = 'lytix_helper';

/**
 * Logs an error and replaces a widget’s content with an error message.
 *
 * @method handleError
 * @param {Error} error The Error that has been thrown;
 *    should also provide ‘stringKey’ for fetching the language string for the user facing message.
 * @param {String} widgetId The widget’s id in the DOM.
 */
const handleError = (error, widgetId) => {
    const widgetContent = document.querySelector('#' + widgetId + ' .content');
    widgetContent.classList.add('widget-error');

    // XXX: Might not work if the node contains SVG.
    // Maybe it’s better to replace node entirely?
    widgetContent.innerHTML = '';

    const
        frag = document.createDocumentFragment(),
        span = document.createElement('span'),
        insertMessage = message => {
            span.innerText = message;
            frag.appendChild(span);
            widgetContent.appendChild(frag);
            return;
        };

    // eslint-disable-next-line promise/no-promise-in-callback
    Str.get_string(error.stringKey || 'generic_error', moodleComponent)
    .then(string => insertMessage(string))
    .catch(error => {
        insertMessage('Something went wrong. Please try reloading or report this incident if the error persists.');
        Log.debug('Some supplied data could probably not be processed correctly.', error);
    });

    // An element might be hidden by default to be built outside of the DOM.
    widgetContent.removeAttribute('hidden');

    Log.debug(error);
};


/**
 * Fetches language strings; basically a wrapper around Str.get_strings().
 *
 * @method getStrings
 * @param {Object} data An object where each property name is the component from which to fetch strings for the given keys.
 *      Each entry holds another object that needs at least one of the following properties: ‘identical’ or ‘differing’.
 *      The former has to be an array of keys, where each key is also used to access the fetched string later on.
 *      The latter must be an object where each property name is the same that is later used for accessing the string,
 *      with its value being the respective key for fetching.
 * @return {Promise} Resolves into an object where each fetched string can be accessed by either the key provided in ‘identical’
 *      or by its property name from ‘differing’. Otherwise rejects with a key for an appropriate error message.
 *      Caution! This is not a native Promise, it is a promise as defined by jQuery!
 */

/* This is an example input:
 {
    local_plugin_one: {
        differing: {
            error: 'error_msg',
            warning: 'warning_msg',
            success: 'success_msg',
        },
        identical: [ 'person', 'name', 'student' ],
    },
    // here, ‘differing’ is optional because ‘identical’ is supplied
    core_stuff: {
        identical: [ 'grades', 'scores', 'results' ],
    },
} */
const getStrings = data => {
    const
        components = Object.keys(data),
        requests = [], // This is what Str.get_strings() wants.
        referenceKeys = [], // A array of arrays containing keys for later access.
        lengths = [];

    for (const componentName of components) {
        const component = data[componentName];
        if ('identical' in component) {
            const
                keys = component.identical,
                length = keys.length;
            for (let i = 0; i < length; ++i) {
                requests.push({
                    key: keys[i],
                    component: componentName,
                });
            }
            referenceKeys.push(keys);
            lengths.push(keys.length);
        }
        if ('differing' in component) {
            const
                differing = component.differing,
                objectKeys = Object.keys(differing),
                length = objectKeys.length;
            for (let i = 0; i < length; ++i) {
                requests.push({
                    key: differing[objectKeys[i]],
                    component: componentName,
                });
            }
            referenceKeys.push(objectKeys);
            lengths.push(objectKeys.length);
        }
    }
    return Str.get_strings(requests).then(strings => {
        const
            result = {},
            count = lengths.length;
        let stringIndex = 0;
        for (let i = 0; i < count; ++i) {
            const length = lengths[i];
            const keys = referenceKeys[i];
            for (let j = 0; j < length; ++j) {
                result[keys[j]] = strings[stringIndex++];
            }
        }
        return result;
    })
    .catch(error => {
        Log.debug(error);
        error.stringKey = 'fetch_failed';
        throw error;
    });
};


/**
 * Fetches data and allows validating it.
 *
 * @method getData
 * @param {string} methodname The method to run.
 * @param {args} args An object containing the required arguments for the method (usually courseid and contextid).
 * @return {Promise} Resolves into the fetched object; or rejects with a key for an appropriate error message.
 */
const getData = (methodname, args) => {
    return Ajax.call([{
        methodname: methodname,
        args: args,
    }])[0]
    .then(data => data)
    .catch(error => {
        error.stringKey = 'fetch_failed';
        throw error;
    });
};


/*
Import Templates from 'core/templates';
const makeExportFunction = (widgetId) => {
    // get export button
    // assign function using Templates.render() to export button
};
*/

/**
 * Convert Moodle’s locale to one that can be used with JS.
 *
 * @method convertLocale
 * @param {string} locale The locale provided by Moodle (https://docs.moodle.org/dev/Table_of_locales#Table)
 * @return {string} https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl#locales_argument
 */
const convertLocale = locale => locale.substring(0, locale.indexOf('.')).replace('_', '-');

/**
 * Get a widget’s content container.
 *
 * @method getContentContainer
 * @param {string} widgetId The widget’s id.
 * @return {HTMLElement} The widget’s content
 */
const getContentContainer = widgetId => document.querySelector('#' + widgetId + ' .content');

export default {
    NoDataError,
    handleError,
    getStrings,
    getData,
    convertLocale,
    getContentContainer,
};
