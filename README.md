# opengamedata-logger

## Services

The data is logged with the [log.php](https://github.com/opengamedata/opengamedata-logger/blob/master/log.php) file contained in this repository, which is hosted at the following URL:

`https://fielddaylab.wisc.edu/logger/log.php`

HTTP post requests made to the database should include this URL as the base of the request string.

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

The data will be insterted into the database at `fieldday-db.ad.education.wisc.edu`, and later relocated to be stored at `fieldday-store.ad.education.wisc.edu`.

## Examples

Sample documentation of logging events and categories can be found in [Field Day's Lakeland repository](https://github.com/fielddaylab/lakeland). Lakeland also contains the logging file [`simplelog.js`](https://github.com/fielddaylab/lakeland/blob/master/src/simplelog.js), which demonstrates how a JavaScript object can take in data and send it to the database with a POST request.

The Unity package found in [opengamedata-unity](https://github.com/opengamedata/opengamedata-unity) also contains a [C# implementation of the Lakeland `simplelog`](https://github.com/opengamedata/opengamedata-unity/blob/main/Assets/FieldDay/SimpleLog.cs), which can be used for logging in Unity projects, or serve as a model for implementing this logging functionality with another language as needed.
