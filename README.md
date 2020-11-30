# opengamedata-logger

## Usage

Data is logged with the [`log.php`](https://github.com/opengamedata/opengamedata-logger/blob/master/log.php) file contained in this repository, which is hosted at the following URL:

`https://fielddaylab.wisc.edu/logger/log.php`

HTTP post requests made to the database should include this URL as the base of the request string.

Sending logs to the database can be handled by a single script which builds a request URL with the necessary information for the database, and contain functions for logging and flushing that data to the given URL.

The full request string should be formatted as follows:

`https://fielddaylab.wisc.edu/logger/log.php?app_id={1}&app_version={2}&session_id={3}&persistent_session_id={4}&player_id={5}&req_id={6}`

1. `app_id`: identifier for given app, should match the game's name in the database
2. `app_version`: the current version of the app
3. `session_id`: a unique ID for the current game session
4. `persistent_session_id`: ID for a persistent browser session, handled using cookies
5. `player_id`: if specified, redirects to a unique page for the given player
6. `req_id`: a unique identifier for a single POST request to the database

In [previous implementations](#examples), string is built within the logging object's constructor. A logging function should then take in a properly formatted dictionary, add keys for session number and client time, and add the dictionary to an accrued list of data. A flush function can then be called, which sends an HTTP request to the specified URL, and flushes logs stored into the accrued list to the database in that POST request.

The data will be insterted into the database hosted at `fieldday-db.ad.education.wisc.edu`, and later relocated to be stored at `fieldday-store.ad.education.wisc.edu`.

## Database Categories

Logs within the database contain the following categories:

- `app_id_fast`: identifier for the given app, in this case should be the game's name
- `event_custom`: the event category if the logged event is custom
- `event_data_complex`: a JSON string containing all the logged information
- `event_data_simple`: currently unused, but still needs to be present in the log
- `persistent_session_id`: ID used for persistence, stored in a client cookie
- `client_time`: client's timestamp resolved to the second
- `client_time_ms`: client's timestamp in number of milliseconds
- `session_n`: integer that increments with each log, showing the true order of the logs
- `http_user_agent`: string representing the HTTP user agent

When sending data to the logger, it should be formatted in a dictionary such that an `event_custom` key maps to the event category for that data. All data information should then be stored as a JSON string mapped to an `event_data_complex` key (see the `send_log` function in [Lakeland's logging implementation](https://github.com/fielddaylab/lakeland/blob/master/src/logging.js#L725)).

<a name="examples"/>

## Examples

A JavaScript implementation of send data to the logger can be found in [Field Day's Lakeland repository](https://github.com/fielddaylab/lakeland/blob/master/src/simplelog.js). This script also uses helper functions for generating unique IDs and getting/setting browser cookies, which can be found in a [separate utilities file](https://github.com/fielddaylab/lakeland/blob/master/src/utils.js#L2087).

[Open Game Data's Unity package](https://github.com/opengamedata/opengamedata-unity/blob/main/Assets/FieldDay/SimpleLog.cs) provides an example of how this JavaScript code can easily be rewritten in a different language as a project may require.
