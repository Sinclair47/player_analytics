# Change Log
All notable changes to this project will be documented in this file.

## 2016-08-18
### Added
- Staff whitelist
- Staff & Player info flag names
- crossroads.js for routing support. All pages are accessible via url now
- simple error/404 page
- It is possible to select multiple servers now
- You can add multiple DBs now and switch easy between them
- A simple cache

## Changed
- The DB configuration is now in config_db.php.
- Server selection now works globally on all pages
- Date selection now works globally on all pages


## Fixed
- Dashboard Charts are shown broken with empty values
- Some layout issues

## 2016-08-11
### Added
- Staff page: Show only players with flags
- Mark the current nav element as active
- Table name constant for easier development

## Changed
- Add a third option "unknown" in the Premium/F2P chart
- update js/css files
- Optimized SQL query in SSP class for better performance. WHERE is much faster then HAVING with GROUP BY

## Fixed
- Issue #2
- Sending muliple ajax requests in single server views


## 2016-08-08
### Added
- server list (repo merge)

### Changed
- Show also years and months in the "Hours Played" box

### Fixed
- Connect method chart

## 2015-08-07
### Added
- server list (repo merge)

### Changed
- Show the Top 10 countries (you can edit it in the config)

### Fixed
- Connect method chart
