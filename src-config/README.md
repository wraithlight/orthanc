## src-config

### `configuration.json`
This is the main configuration file for the application. The file contains six different levels:
* `app_web`
* `app_desktop`
* `app_mobile`
* `common`
* `server`

Basically all of these are extending the `common` property, these are more like overrides on top of common.

Inheritances
* `app_*` > `app_common` > `common`
* `server` > `common`
