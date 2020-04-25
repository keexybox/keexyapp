<?php
/**
 * @author Benoit Saglietto <bsaglietto[AT]keexybox.org>
 *
 * @copyright Copyright (c) 2020, Benoit SAGLIETTO
 * @license GPLv3
 *
 * This file is part of Keexybox project.

 * Keexybox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Keexybox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Keexybox.	If not, see <http://www.gnu.org/licenses/>.
 *
 */

$conf_data = "
// GOOGLE
zone \"www.google.com\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ad\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ae\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.af\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ag\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ai\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.al\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.am\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.ao\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ar\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.as\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.at\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.au\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.az\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ba\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.bd\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.be\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.bf\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.bg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.bh\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.bi\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.bj\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.bn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.bo\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.br\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.bs\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.bt\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.bw\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.by\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.bz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ca\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cd\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cf\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ch\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ci\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.ck\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cl\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.co\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.cr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.cu\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cv\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.cy\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.de\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.dj\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.dk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.dm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.do\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.dz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ec\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ee\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.eg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.es\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.et\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.fi\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.fj\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.fm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.fr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ga\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ge\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.gg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.gh\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.gi\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.gl\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.gm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.gp\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.gr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.gt\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.gy\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.hk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.hn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.hr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ht\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.hu\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.id\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ie\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.il\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.im\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.in\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.iq\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.is\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.it\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.je\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.jm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.jo\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.jp\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.ke\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.kh\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ki\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.kg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.kr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.kw\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.kz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.la\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.lb\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.li\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.lk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.ls\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.lt\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.lu\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.lv\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ly\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.ma\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.md\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.me\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.mg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.mk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ml\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.mm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.mn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ms\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.mt\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.mu\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.mv\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.mw\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.mx\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.my\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.mz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.na\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.nf\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ng\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ni\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ne\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.nl\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.no\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.np\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.nr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.nu\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.nz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.om\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.pa\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.pe\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.pg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ph\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.pk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.pl\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.pn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.pr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ps\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.pt\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.py\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.qa\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ro\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ru\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.rw\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.sa\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.sb\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.sc\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.se\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.sg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.sh\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.si\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.sk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.sl\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.sn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.so\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.sm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.sr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.st\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.sv\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.td\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.tg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.th\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.tj\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.tk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.tl\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.tm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.tn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.to\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.tr\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.tt\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.tw\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.tz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.ua\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.ug\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.uk\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.uy\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.uz\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.vc\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.ve\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.vg\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.vi\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.com.vn\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.vu\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.ws\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.rs\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.za\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.zm\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.co.zw\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};

zone \"www.google.cat\" {
        type master;
        file \"".$this->bind_root_dir."/etc/zones/safesearch_google.zone\";
        allow-query { any; };
};
"
?>
