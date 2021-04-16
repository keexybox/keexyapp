KeexyBox Project
============


[![KeexyBox License](https://img.shields.io/static/v1?label=license&message=GPLv3&color=blue)](http://www.gnu.org/licenses/)
[![CakePHP version](https://img.shields.io/static/v1?label=CakePHP&message=v3.8.10&color=red)](https://cakephp.org/)
[![AdminLTE version](https://img.shields.io/static/v1?label=AdminLTE&message=v2.4.18&color=green)](https://adminlte.io/)


## Presentation
**KeexyBox** allows you to do **parental control**, **block ads**, limit telemetry, and **browse the Internet anonymously** from your home network without installing any software on your devices. It also can be used to create a public **wireless access point** with the **captive portal**. It is a software program which requires a [Raspberry PI](https://www.raspberrypi.org/) box and which is installed on [Raspbian](https://www.raspberrypi.org/downloads/raspbian/). It constitutes a cut-off point between your devices (computers, tablets, smartphones, etc.) and your router or Internet box. KeexyBox acts as a default gateway and DNS server for the devices in your home network.

It thus intercepts all connections to the Internet to carry out website filtering or to activate browsing via the Tor anonymity network according to connection profiles which you have configured.

!["KeexyBox diagram"](https://keexybox.org/wp-content/uploads/2020/10/keexybox_net_topology_as_gateway_v2.png "KeexyBox diagram")

It does not require any installation on your devices because the filtering is carried out by the DNSs, thus allowing HTTPS sites to be filtered while at the same time protecting the KeexyBox users’ privacy.

**Learn more and Download on [KeexyBox.org](https://keexybox.org).**

## Documentation & Installation Guide

Visit the [online documentation](https://wiki.keexybox.org).

## Contribution
KeexyBox is mainly based off of [CakePHP Framework](https://cakephp.org/) and [AdminLTE](https://adminlte.io/) for the Web interface. By knowing these two frameworks you can easily make your contribution to the development of KeexyBox.

Contribution are always **welcome and recommended**! Here is how:

- Fork the repository ([here is the guide](https://help.github.com/articles/fork-a-repo/)).
- Clone to your machine ```git clone https://github.com/keexybox/keexyapp```
- Make your changes
- Create a pull request

### Contribution Prerequisites:

You will have to compile extra softwares like [ISC Bind](https://www.isc.org/bind/), [ISC DHCP](https://www.isc.org/dhcp/) and [Tor](https://www.torproject.org/) that are used by KeexyBox ([here is the guide](https://wiki.keexybox.org/doku.php/manual_installation)).

### Contribution Requirements:

- When you contribute, you agree to give a non-exclusive license to KeexyBox.org to use that contribution in any context as we (KeexyBox.org) see appropriate.
- If you use content provided by another party, it must be appropriately licensed using an [open source](http://opensource.org/licenses) license.
- Contributions are only accepted through Github pull requests.
- Finally, contributed code must work in all supported browsers (see above for browser support).

## License
KeexyBox is an open source project by [KeexyBox.org](https://keexybox.org) that is licensed under [GPLv3](https://www.gnu.org/licenses).

## Change log
**For the most recent change log, visit the [releases page](https://github.com/keexybox/keexyapp/releases) or the [changelog file](https://github.com/keexybox/keexyapp/blob/master/CHANGELOG.md).** We will add detailed release notes to each new release. 

## Getting in touch with KeexyBox's project
- [Facebook](https://www.facebook.com/keexybox)
- [Twitter](https://twitter.com/keexybox)
- [KeexyBox's wiki](https://wiki.keexybox.org)
- [KeexyBox's forum](https://forum.keexybox.org)

## Donations
Donations are **greatly appreciated!**

[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif "KeexyBox Donate")](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=L5WFG252CDR2A&source=url "Donate")
