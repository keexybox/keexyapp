# Changelog

All notable changes on KeexyApp for KeexyBox is documented below.

## [21.04.2] - 2021-05-11

### Changed
 - Fix issue [#20](https://github.com/keexybox/keexyapp/issues/20)

## [21.04.1] - 2021-05-05

### Added
 - Possibility to define a default profile for the entire network. This allows devices to be connected to the internet without having to declare them in KeexyBox.
 - Possibility of using the captive portal to override the connection profile defined for the entire network.
 - Possibility to customize the captive portal with a name and a logo
 - Possibility of enable/disable DHCP independently on the input network and the output network (Requires 2 independent interfaces)

### Changed
 - Do not activate the Wifi access point when no wireless interface is available
 - WUI updated AdminLTE from 2.3 to 2.4

## [20.10.2] - 2021-02-01

### Added
 - Links and useful tools for users in the login information page
 - Add check and update from WebUI

### Changed
 - Fixed an issue when updating the Apache configuration
 - Fix issue [#11](https://github.com/keexybox/keexyapp/issues/11)

## [20.10.1] - 2020-10-07

### Added
 - Wireless Access Point
 - Captive Portal feature that can be set for a public Wireless Access Point
 - Allow users to edit or delete their account
 - The ability to set countries for Tor exit nodes

### Changed
 - Some misc system settings moved to captive portal settings
 - Fix issue [#9](https://github.com/keexybox/keexyapp/issues/9)

## [20.04.4] - 2020-08-24

### Changed
 - Fix SQL error when editing firewall rule

## [20.04.3] - 2020-08-20

### Changed
 - Fix issue [#7](https://github.com/keexybox/keexyapp/issues/7)

## [20.04.2] - 2020-05-15

### Added
 - Display KeexyBox version in https://keexyboxaddress/help/licenses
 - Force Firefox to disable DNS over HTTPS when enabled by default

### Changed
 - Fix issue [#4](https://github.com/keexybox/keexyapp/issues/4)

## [20.04.1] - 2020-04-25

### Added
 - Initial release
