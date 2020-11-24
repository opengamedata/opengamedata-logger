# opengamedata-logger

## Setup

## Services

The data is logged with the [log.php](https://github.com/opengamedata/opengamedata-logger/blob/master/log.php) file contained in this repository, which is hosted at the following URL:

`https://fielddaylab.wisc.edu/logger/log.php`

HTTP post requests made to the database should include this URL as the base of the request string.

## Database Categories

Logs within the database contain the following categories:

- `event_custom`: the event category if the logged event is custom
- `event_data_complex`: a JSON string containing all the logged information
- `event_data_simple`: currently unused, but still needs to be present in the log
- `persistent_session_id`: ID used for persistence, stored in a client cookie
- `client_time`: client's timestamp resolved to the second
- `client_time_ms`: client's timestamp in number of milliseconds
- `session_n`: integer that increments with each log, showing the true order of the logs
- `http_user_agent`: string representing the HTTP user agent
