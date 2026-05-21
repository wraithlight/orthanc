## src-config

### `configuration.json`
This is the main configuration file for the application. The file contains six different levels:
* `app_web`
* `app_desktop`
* `app_mobile`
* `app_common`
* `common`
* `server`

Basically all of these are extending the `common` property, these are more like overrides on top of common.

Inheritances
* `app_*` > `app_common` > `common`
* `server` > `common`

`app_web`, `app_desktop`, `app_mobile` and `app_common` are showing the same semantics as `common` but each property is optional. Same applies to `server`.
Do not forget that `server` and `app_common` are also extending the `common` configuration.
