# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## 1.3.6 – 2021-06-18
### Fixed
- php 7.2 compatibility

## 1.3.5 – 2021-06-09
### Changed
- replace home made user search by OCS search request
- refactor and cleanup project service
- use comment field when searching bills
- display date, time and currency in unified search results

### Fixed
- issue with global user search in some cases
[#54](https://github.com/eneiluj/cospend-nc/issues/54) @susinths
- auto delete shares with unexisting users

## 1.3.4 – 2021-05-06
### Fixed
- fix payment mode in stats again
[#52](https://github.com/eneiluj/cospend-nc/issues/52) @jonasbb @Byter3

## 1.3.3 – 2021-05-06
### Fixed
- missing fallback payment mode in stats
[#52](https://github.com/eneiluj/cospend-nc/issues/52) @jonasbb @Byter3

## 1.3.2 – 2021-05-03
### Added
- new option to display/set dates instead of datetimes
[#46](https://github.com/eneiluj/cospend-nc/issues/46) @pawlosck
- bill repetition frequency for daily, weekly, monthly and yearly
[#47](https://github.com/eneiluj/cospend-nc/issues/47) @pawlosck
- payment mode stats (monthly and global)

### Changed
- new bill default time is 00:00:00
[#46](https://github.com/eneiluj/cospend-nc/issues/46) @pawlosck

### Fixed
- use absolute URLs for activity icons
- fix perso amounts and custom owed amount with coma
[#44](https://github.com/eneiluj/cospend-nc/issues/44) @ndi123
- fix strange JS number operations behaviour
[#44](https://github.com/eneiluj/cospend-nc/issues/44) @ndi123
- fix member avatar color with Avatar wrapper component handling custom color prop
- use user timezone in statistics generation
[#45](https://github.com/eneiluj/cospend-nc/issues/45) @pawlosck
- bill deletion activity
- avoid some db queries in activity manager

## 1.3.0 – 2021-03-15
### Added
- individual settlement
[#29](https://github.com/eneiluj/cospend-nc/issues/29) @FrouxBY

### Changed
- adjust project navigation item icons

### Fixed
- custom mode selected for existing bills when switching from new bill
[#42](https://github.com/eneiluj/cospend-nc/issues/42) @mayonezo
- get rid of old avatars, reduces load on server
[#37](https://github.com/eneiluj/cospend-nc/issues/37) @normen

## 1.2.9 – 2021-01-29
### Changed
- use svg icon instead of UTF-8 char

### Fixed
- bump nc-vue to fix issue with iOS devices
[#33](https://github.com/eneiluj/cospend-nc/issues/33) @ndi123

## 1.2.8 – 2021-01-19
### Changed
- bump js libs
- add move icon next to draggable categories
- automated release process is now launched locally

### Fixed
- db queries with functions, issues with Postgres
[#30](https://github.com/eneiluj/cospend-nc/issues/30) @lkempf
- popover scroll problem

## 1.2.7 – 2020-12-27
### Added
- option to disable bill deletion
[#13](https://github.com/eneiluj/cospend-nc/issues/13) @LJJS
- choose between alphabetical and manual categories order
[#26](https://github.com/eneiluj/cospend-nc/issues/26) @itatabitovski
- reorder categories by drag'n'drop
[#26](https://github.com/eneiluj/cospend-nc/issues/26) @itatabitovski
- new bill creation mode 'custom shares' (ignoring member weights or not)
[#20](https://github.com/eneiluj/cospend-nc/issues/20) @stildalf

### Changed
- improve categories and currencies design

### Fixed
- bills count and total spent in sidebar

## 1.2.4 – 2020-12-19
### Added
- biweekly and semi monthly repetition frequencies
[#19](https://github.com/eneiluj/cospend-nc/issues/19) @DrMantisTobbogan
- hint about repetition in UI
- button to repeat a bill 'now'
[#22](https://github.com/eneiluj/cospend-nc/issues/22) @Nadeige

### Changed
- improve simple member multiselect item design
- refactor loops and network calls

### Fixed
- parse GET params and select project if an ID was given
- add missing primary key for projects table
[#24](https://github.com/eneiluj/cospend-nc/issues/24) @acsfer

## 1.2.2 – 2020-11-26
### Added
- animations in bill list and stats

### Changed
- improve last stats 'who paid for whom' table
- code quality improvements in backend and frontend
- bump js libs

## 1.2.1 – 2020-11-13
### Added
- new field when settling: max date, impacts table, auto settlement and settlement export
- new stats table to know who paid for whom

### Changed
- improve style of bill item counter

### Fixed
- line charts didn't fill color of first dataset
- members monthly stats bug with disabled members
- crash when missing window.OCA.Theming.cacheBuster
[#17](https://github.com/eneiluj/cospend-nc/issues/17) @xetyr

## 1.2.0 – 2020-11-08
### Added
- automatic releases with GitHub actions
- automatically add/remove shared access to NC users when adding/removing them as project members
[#6](https://github.com/eneiluj/cospend-nc/issues/6) @simonspa
- empty content for project list, bill list, main content
[#7](https://github.com/eneiluj/cospend-nc/issues/7) @simonspa
- loading icons here and there
- multi selection mode to delete or assign category/payment mode to multiple bills @pichette
- partial initial bill list load, load the rest with infinite scrolling
[#11](https://github.com/eneiluj/cospend-nc/issues/11) @simonspa
- protocol based mobile client QRCode links

### Changed
- bump all JS libs
- improve some labels
- use AppContentDetails component for main content (bill form, stats, settlement)
- big style improvements in stats and bill form
- move GitLab wiki to files in the repo
- improve dashboard widget empty content
- declare navigation in info.xml
- more popovers (cat/cur management, on stats table rows...)
- style cleanup, most images are now loaded by webpack

### Fixed
- ambiguous
- all eslint errors/warnings, no more props mutation
- internal search reacting to wrong event

## 1.1.4 – 2020-10-19
### Fixed
- another custom icon path...

## 1.1.3 – 2020-10-18
### Fixed
- custom icon path

## 1.1.2 – 2020-10-16
### Changed
- move to GitHub
- use Webpack 5
- use stylelint
- get rid of vue2-transitions

### Fixed
- major UI performance problem: render category edition only in edition mode (emoji picker was loaded many times)

## 1.1.1 – 2020-10-14
### Changed
- bump vue libs
- use Psr logger

### Fixed
- hide member actions menu if user is not at least maintainer
- fix padding of disabled mask in some cases
- unified search now triggers internal search as well
- fix paginated unified search
- allow 2 digits member weight
[#121](https://gitlab.com/eneiluj/cospend-nc/issues/121) @ratte-rizzo

## 1.1.0 – 2020-09-15
### Added
- option to choose project list sorting criteria
[#114](https://gitlab.com/eneiluj/cospend-nc/issues/114) @ratte-rizzo
- unified search for bills
- dashboard widget showing activity
- add 'max precision' setting to display correct precise balances
[#117](https://gitlab.com/eneiluj/cospend-nc/issues/117) @ja-nko

### Changed
- use latest nc-vue 2.6.5
- use nc-vue emoji picker instead of emoji-button
- many design improvements in category/currency management, qrcode hints...

### Fixed
- clean UI code, now eslint-compliant
- really delete members when possible
[#116](https://gitlab.com/eneiluj/cospend-nc/issues/116) @mrbenjoi

## 1.0.5 – 2020-08-03
### Fixed
- String.replaceAll does not exist in chrome based browsers
[#113](https://gitlab.com/eneiluj/cospend-nc/issues/113) @Neutrino1986

## 1.0.4 – 2020-08-02
### Changed
- show 'delete' for members with balance close to 0

### Fixed
- fix comma replacement in simple math
[#15](https://gitlab.com/eneiluj/cospend-nc/issues/15) @simonspa
- sharing access level disabled conditions
- don't show public links with more permissions than the current user
- settlement bills payment mode was not set
[#112](https://gitlab.com/eneiluj/cospend-nc/issues/112) @simonspa

## 1.0.3 – 2020-07-24
### Added
- simple math for amount fields in bill form
[#15](https://gitlab.com/eneiluj/cospend-nc/issues/15) @rouvenV
- a few animations with vue2-transitions
- member (and access) management in settings sidebar tab

### Changed
- no more JQuery, using axios for ajax requests
- improve QRCode component
- move QRCode to sharing sidebar tab
- improve category management style
- improve date display and use nc-vue datetimepicker
- improve bill form design
- select all owers by default in new bill
- update nv-vue components

### Fixed
- duplicated route problem
- bug when doing repetitive shared access add/del
- disabled flag was not sent in member edition request
- bill form display on mobile view
[#109](https://gitlab.com/eneiluj/cospend-nc/issues/109) @Joniator
- delay member color edition to avoid many requests

## 1.0.1 – 2020-07-01
### Fixed
- restore old route to allow MoneyBuster getting project list

## 1.0.0 – 2020-07-01
### Changed
- complete UI rewrite in Vue.js
[#103](https://gitlab.com/eneiluj/cospend-nc/issues/103) @call-me-matt @archit3kt @simonspa @newhinton

### Fixed
- avoid some backend crashes related to circles

## 0.5.5 – 2020-06-03
### Added
- bill counter
[#100](https://gitlab.com/eneiluj/cospend-nc/issues/100) @miguelangel.caballerobracero

### Changed
- improve payer/ower naming (you -> all except A, B)
[#101](https://gitlab.com/eneiluj/cospend-nc/issues/101) @call-me-matt
- begin to use vue.js, currency management fully converted

### Fixed
- import project (some bills were missing)
[#100](https://gitlab.com/eneiluj/cospend-nc/issues/100) @miguelangel.caballerobracero
- amount preview with negative amount
[#100](https://gitlab.com/eneiluj/cospend-nc/issues/100) @miguelangel.caballerobracero
- include port number in guest link
[#102](https://gitlab.com/eneiluj/cospend-nc/issues/102) @singulosta

## 0.5.4 – 2020-05-23
### Changed
- use more placeholders
- improve QRCode generation
[#42](https://gitlab.com/eneiluj/cospend-nc/issues/42) @call-me-matt

### Fixed
- new bill in guest access page
[#99](https://gitlab.com/eneiluj/cospend-nc/issues/99) @pflegende

## 0.5.3 – 2020-05-17
### Changed
- shift+enter keybinding to create bill
[#97](https://gitlab.com/eneiluj/cospend-nc/issues/97) @nicoe

### Fixed
- fix public page, don't register search
[#98](https://gitlab.com/eneiluj/cospend-nc/issues/98) @DavidMStraub
- avoid getting member avatar in public pages

## 0.5.2 – 2020-05-09
### Added
- now able to center settlement on one member
[MB#28](https://gitlab.com/eneiluj/moneybuster/issues/28) @patxiku

### Fixed
- online service payment mode mistake

## 0.5.1 – 2020-05-07
### Fixed
- mistake in getBills when using 'lastchanged'
[#96](https://gitlab.com/eneiluj/cospend-nc/issues/96) @simonspa

## 0.5.0 – 2020-05-07
### Added
- new payment mode: online service

### Changed
- improve search
[!169](https://gitlab.com/eneiluj/cospend-nc/-/merge_requests/169) @simonspa
- improve splitwise import: import categories
[#95](https://gitlab.com/eneiluj/cospend-nc/issues/95) @madevr
- optimize SQL queries when getting bill list
[#95](https://gitlab.com/eneiluj/cospend-nc/issues/95) @madevr

### Fixed
- missing currency selection field
[!170](https://gitlab.com/eneiluj/cospend-nc/-/merge_requests/170) @simonspa
- splitwise import wrong timestamp
[#95](https://gitlab.com/eneiluj/cospend-nc/issues/95) @madevr

## 0.4.9 – 2020-05-02
### Added
- now possible to link Nextcloud user with project member
[!166](https://gitlab.com/eneiluj/cospend-nc/-/merge_requests/166) @Plunts
- basic search in bill list with Nextcloud search field
- new "comment" field for bills

### Changed
- escape key closes some edition areas
- use avatars in every possible place (stats, shared access, balance, bill item...)
- simpler translation updates management

### Fixed
- number formatting in category stats
[!167](https://gitlab.com/eneiluj/cospend-nc/-/merge_requests/167) @Plunts
- use timestamp for date filters, make filter bounds inclusive
- always encode utf-8 symbols to avoid database problems
[#92](https://gitlab.com/eneiluj/cospend-nc/issues/92) @loetermann @lenalebt @fperget
- allow comas in fields, fix export/import accordingly

## 0.4.6 – 2020-04-16
### Changed
- no more dirty html string generation, use jquery instead

### Fixed
- bug in bill repetition

## 0.4.5 – 2020-04-07
### Added
- monthly stats per category and per member (table and chart)
[!165](https://gitlab.com/eneiluj/cospend-nc/-/merge_requests/165) @simonspa

### Changed
- refactor JS
[!160](https://gitlab.com/eneiluj/cospend-nc/-/merge_requests/160) @chiefbrain
- convert hardcoded categories to real ones that can be edited/deleted
[#87](https://gitlab.com/eneiluj/cospend-nc/issues/87) @simonspa
- don't ask for project id anymore when creating a project, generate it from project name
[#90](https://gitlab.com/eneiluj/cospend-nc/issues/90) @g--work

### Fixed
- simpler export name to fix import
[#89](https://gitlab.com/eneiluj/cospend-nc/issues/89) @simonspa
- fix export, missing timestamp
[#89](https://gitlab.com/eneiluj/cospend-nc/issues/89) @simonspa
- design fixes
- bug with some project ids

## 0.4.4 – 2020-03-31
### Added
- now able to share projects with multiple public links with specific roles
[#80](https://gitlab.com/eneiluj/cospend-nc/issues/80) @call-me-matt
- right click open items context menu
- emoji picker for category icon

### Changed
- share permissions become roles (viewer, participant, maintener, admin)
[#80](https://gitlab.com/eneiluj/cospend-nc/issues/80) @call-me-matt
- improve project sharing design a lot
- remove external project feature
- bill edition design/icons improvements
- improve settlement table design

### Fixed
- clearer labels for project id/name/title
[#81](https://gitlab.com/eneiluj/cospend-nc/issues/81) @call-me-matt
- weight edition bugs @archit3kt
- webkit style compatibility

## 0.4.2 – 2020-03-23
### Fixed
- guest permissions edition in public access
[#80](https://gitlab.com/eneiluj/cospend-nc/issues/80) @call-me-matt
- remove constraint on bill breaking link to public file
[#83](https://gitlab.com/eneiluj/cospend-nc/issues/83) @Dunkelschunkel

## 0.4.1 – 2020-03-22
### Added
- new translations

### Fixed
- add constraints on many fields (back and frontend)
- mistake in add bill public api
[#79](https://gitlab.com/eneiluj/cospend-nc/issues/79) @call-me-matt

## 0.4.0 – 2020-03-19
### Added
- add 'time' field for bills
[#48](https://gitlab.com/eneiluj/cospend-nc/issues/48) @mikoladz @rexkani
- private API route to create project as a user

### Changed
- improve CSV project import
- switch to npm+webpack!

### Fixed
- fix 'all except reimbursement' stat filter
[#77](https://gitlab.com/eneiluj/cospend-nc/issues/77) @jonfin

## 0.3.3 – 2020-02-22
### Added
- occ export-project command
[#69](https://gitlab.com/eneiluj/cospend-nc/issues/69) @schwerpunkt
- bank transfer payment mode
- circle share
[#31](https://gitlab.com/eneiluj/cospend-nc/issues/31) @sunjam1
- show filtered balance in stats (if different from general balance)
[#58](https://gitlab.com/eneiluj/cospend-nc/issues/58) @archit3kt

### Changed
- project screenshots
- improve error messages
- upgrade tools used in CI

### Fixed
- remove minimum value for 'amount' field in bill edition
[#72](https://gitlab.com/eneiluj/cospend-nc/issues/72) @schwerpunkt
- avoid mess when changing displayed bill during saving request
[#73](https://gitlab.com/eneiluj/cospend-nc/issues/73) @schwerpunkt

## 0.3.2 – 2020-01-23
### Added
- project currencies management and conversion
[#36](https://gitlab.com/eneiluj/cospend-nc/issues/36)
[#46](https://gitlab.com/eneiluj/cospend-nc/issues/46) @archit3kt @Allirion @deepbluev7 @puerki
- custom categories
[#65](https://gitlab.com/eneiluj/cospend-nc/issues/65) @Helloha

### Changed
- show avatars where it's possible
- improve spent value display in bill edition form
[#68](https://gitlab.com/eneiluj/cospend-nc/issues/68) @jaroslaw.gerin
- improve weight value display in member list item
[#68](https://gitlab.com/eneiluj/cospend-nc/issues/68) @jaroslaw.gerin

### Fixed
- use proper templates for public pages (guest access)
- utf8 characters in avatar
[#67](https://gitlab.com/eneiluj/cospend-nc/issues/67) @jaroslaw.gerin

## 0.3.0 – 2020-01-08
### Added
- option to change output directory
[#57](https://gitlab.com/eneiluj/cospend-nc/issues/57) @xsus95
- permissions for guest access and user/group shares
[#34](https://gitlab.com/eneiluj/cospend-nc/issues/34) @yward
- option to include all active members when repeating a bill
[#53](https://gitlab.com/eneiluj/cospend-nc/issues/53) @quizilkend
- new REST API which requires login
- able to import projects files exported from splitwise
[!152](https://gitlab.com/eneiluj/cospend-nc/merge_requests/152) @denics
- new 'reimbursement' category that can be used to filter stats
[#24](https://gitlab.com/eneiluj/cospend-nc/issues/24) @mr-manuel
- able to set a max repetition date
[#29](https://gitlab.com/eneiluj/cospend-nc/issues/29) @eldiep
- monthly stats
[#23](https://gitlab.com/eneiluj/cospend-nc/issues/23) @mr-manuel
- pie and polar area charts in stats
- now possible to edit member color
- show amount owed by each member in bill form

### Changed
- design improvements
- improve category list
[#58](https://gitlab.com/eneiluj/cospend-nc/issues/58) @archit3kt
- improve disabled design
- make all tables sortable
- now able to import/export all bill/members values
- move 'create bill(s)' button

### Fixed
- don't include disabled members when repeating
[#53](https://gitlab.com/eneiluj/cospend-nc/issues/53) @quizilkend
- always get global user balance in stats even with filters
- import/export csv
[#53](https://gitlab.com/eneiluj/cospend-nc/issues/53) @quizilkend
- share icon was hidden by some adblockers
[#53](https://gitlab.com/eneiluj/cospend-nc/issues/53) @quizilkend
- huge bug in bill repetition date condition

## 0.2.0 – 2019-12-16
### Added
- support activity stream for add/del/edit/repeat bill and share/unshare project
- new occ command: cospend:repeat-bills to manually trigger repeat system
- new api route for getBills with more information (to help client to perform partial sync)

### Changed
- refactor controllers code
- use repeat/category/payment mode when exporting/importing

### Fixed
- fix repeat system for 31th
[#49](https://gitlab.com/eneiluj/cospend-nc/issues/49) @PL5bTStMZLduri
[!158](https://gitlab.com/eneiluj/cospend-nc/merge_requests/158) @PL5bTStMZLduri
- fix repeat system if it wasn't triggered during several days
[#49](https://gitlab.com/eneiluj/cospend-nc/issues/49) @eneiluj
- fix some strings and design mistakes
- bug when NC color code is compact

## 0.1.5 – 2019-10-13
### Added
- some categories

## 0.1.4 – 2019-09-14
### Added
- show total payed in statistics
[#43](https://gitlab.com/eneiluj/cospend-nc/issues/43) @nerdoc
- project auto export
- payment modes
[#12](https://gitlab.com/eneiluj/cospend-nc/issues/12) @llucax
[#44](https://gitlab.com/eneiluj/cospend-nc/issues/44) @nerdoc
- bill categories
- statistics filters
[#12](https://gitlab.com/eneiluj/cospend-nc/issues/12) @llucax
[#44](https://gitlab.com/eneiluj/cospend-nc/issues/44) @nerdoc

### Changed
- color management now done by the server avatar service
- sort member list by lowercase name

### Fixed
- fix notification system for NC17

## 0.1.1 – 2019-07-25
### Added

### Changed
- improve settlement process (use https://framagit.org/almet/debts)
- adjust Notifications to NC 17
- compatible with NC >= 17

### Fixed
- make QRCode label more explicit

## 0.1.0 – 2019-05-04
### Added

### Changed
- use Migration DB system
[!81](https://gitlab.com/eneiluj/cospend-nc/merge_requests/81) @werner.schiller
- handle custom server port in links/QRCodes
[#32](https://gitlab.com/eneiluj/cospend-nc/issues/32) @derpeter1

### Fixed
- share autocomplete design
- concurrency problem when creating multiple bills simultaneously
[!111](https://gitlab.com/eneiluj/cospend-nc/merge_requests/111) @klonfish

## 0.0.10 – 2019-04-08
### Changed
- improved user/group sharing design

### Fixed
- avoid 0 weight
[#26](https://gitlab.com/eneiluj/cospend-nc/issues/26) @MoathZ

## 0.0.9 – 2019-04-04
### Changed
- make tests compatible with phpunit 8 (and use it in CI script)
- test with sqlite, mysql and postgresql
- keep validation button for new bill in normal mode
[#14](https://gitlab.com/eneiluj/cospend-nc/issues/14) @swestersund
- change opacity of member name/icon

### Fixed
- fix all/none buttons behaviour for 'personal part' bill
[#14](https://gitlab.com/eneiluj/cospend-nc/issues/14) @swestersund
- fix project selection behaviour (in menu), toggle != select
- fix float-related DB stuff (crashing with PostgreSQL)
- jshint warnings

## 0.0.8 – 2019-03-31
### Fixed
- stupid bug in some SQL queries (was invisible in SQLite...)
[#22](https://gitlab.com/eneiluj/cospend-nc/issues/22) @Questlog

## 0.0.7 – 2019-03-30
### Added
- don't put disabled users in share autocomplete
[#17](https://gitlab.com/eneiluj/cospend-nc/issues/17) @redplanet
- ability to share a project with a group
[#17](https://gitlab.com/eneiluj/cospend-nc/issues/17) @redplanet
- new bill type: even split with personal parts
[#14](https://gitlab.com/eneiluj/cospend-nc/issues/14) @swestersund
- controller tests

### Changed
- use NC DB methods instead of plain SQL
- change share button color when share input is displayed
- test with NC16beta2

### Fixed
- external project renaming field
- UI fix after delete bill error
- replace deprecated addAllowedChildSrcDomain

## 0.0.6 – 2019-03-09
### Added
- CI PhpUnit tests
- QRCode and https link to import project in MoneyBuster
- now able to add external projects (hosted in another Nextcloud instance)

### Changed
- design improvements: selected project bg color
- make password optional for new projects
[#13](https://gitlab.com/eneiluj/cospend-nc/issues/13) @MrCustomizer

### Fixed
- remove settle/stats button from settings

## 0.0.5 – 2019-02-28
### Added
- ability to add public link to NC files in bill name
[#4](https://gitlab.com/eneiluj/cospend-nc/issues/4) @poVoq
- import/export project as csv
[#6](https://gitlab.com/eneiluj/cospend-nc/issues/6) @eneiluj
- export project stats and settlement plan as csv
[#6](https://gitlab.com/eneiluj/cospend-nc/issues/6) @poVoq
- button to apply settlement by automatically adding corresponding bills
[#2](https://gitlab.com/eneiluj/cospend-nc/issues/2) @eneiluj
- option to periodically repeat a bill (day/week/month/year)
[#3](https://gitlab.com/eneiluj/cospend-nc/issues/3) @poVoq
- let user give custom amount per member for new bills => creates several bills
[#7](https://gitlab.com/eneiluj/cospend-nc/issues/7) @poVoq

### Changed
- make app description translatable

### Fixed
- slash is now forbidden in project ID
- add missing loading icons
- balance number display when close to 0
- avoid saving bill if values haven't changed
- SQL queries compat with PostgreSQL

## 0.0.3 – 2019-02-14
### Added
- loading icon everywhere
- display 'no bill' when necessary

### Changed
- UI improvements
- app name : payback -> cospend

### Fixed
- focus on fields when necessary
- remove modern js template string to make l10n.pl work correctly
- avoid one useless browser password saving

## 0.0.2 – 2019-02-07
### Added
- ability to share projects to NC users

## 0.0.1 – 2019-02-01
### Added
- the app

### Changed
- from nothing, it appeared

### Fixed
- fix the world with this app, no more, no less
