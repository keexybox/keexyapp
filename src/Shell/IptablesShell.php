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
 * along with Keexybox. If not, see <http://www.gnu.org/licenses/>.
 *
 */

Namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Log\Log;

/**
 * This class define functions to run Iptables commands
 * This class is mainly used by RulesShell() that organize rules before submitting them to IptablesShell()
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class IptablesShell extends BoxShell
{
    public function main()
    {
        $this->out('Script to manage iptables for Keexybox project');
    }

    /**
     * This function verify if rule exists in iptables 
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to verify rule
     * @param rule : rule to verify
     * 
     * @return integer: return code of command line
     */
    public function VerifyRule($table, $chain, $rule)
    {
        parent::initialize();
        if(isset($table) and isset($chain) and isset($rule)) {
            //$cmd = "$this->bin_sudo $this->bin_iptables -t $table -C $chain $rule";
            $cmd = "$this->bin_iptables -t $table -C $chain $rule";
            $return = $this->RunCmd($cmd);
            return($return['rc']);
        } else {
            return(1);
        }
    }

    /**
     * This function verify if chain exists in iptables 
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to verify rule
     * 
     * @return integer: return code of command line
     */
    public function VerifyChain($table, $chain)
    {
        parent::initialize();
        if(isset($table) and isset($chain)) {
            //$cmd = "$this->bin_sudo $this->bin_iptables -t $table -S $chain";
            $cmd = "$this->bin_iptables -t $table -S $chain";
            $return = $this->RunCmd($cmd);
            return($return['rc']);
        } else {
            return(2);
        }
    }

    /**
     * This function find rule
     *
     * @param table : define filter or nat table
     * @param conditions(Array) : Possibles conditions
     *    chain => chain name (-d iptables option)
     *     src_ip = source ip (-s iptables option)
     *    dst_ip => destination ip (-d iptables option)
     *    target => chain name (-j iptables option)
     * 
     * @return integer: return code of command line
     */
    public function FindRules($table, $conditions=null)
    {
        parent::initialize();
        if(is_array($conditions)) {
            //print_r($conditions);

            $args = null;
            if(isset($conditions['chain'])) {
                $args .= '|'.$this->bin_grep.' "\-A '.$conditions['chain'].'"';
            }

            if(isset($conditions['src_ip'])) {
                $args .= '|'.$this->bin_grep.' "\-s '.$conditions['src_ip'].'"';
            }

            if(isset($conditions['src_mac'])) {
                $args .= '|'.$this->bin_grep.' "\-m mac --mac-source '.$conditions['src_mac'].'"';
            }

            if(isset($conditions['dst_ip'])) {
                $args .= '|'.$this->bin_grep.' "\-d '.$conditions['dst_ip'].'"';
            }

            if(isset($conditions['target'])) {
                $args .= '|'.$this->bin_grep.' "\-j '.$conditions['target'].'"';
            }

            //$cmd = "$this->bin_sudo $this->bin_iptables_save -t $table $args";
            $cmd = "$this->bin_iptables_save -t $table $args";
        }
        if(isset($args)) {
            $return = $this->RunCmd($cmd);
            return($return);
        }
    }

    /**
     * This function flush iptables and set default policies for Keexybox (DROP for filters, ACCEPT for nats)
     *
     */
    public function Start() {
        parent::initialize();
        $rules = [
            " -t filter -F",
            " -t filter -X",
            " -t nat -F",
            " -t nat -X",
            // DEFAULT FILTER POLICY
            " -t filter -P INPUT DROP",
            " -t filter -P FORWARD DROP",
            " -t filter -P OUTPUT DROP",
            // Keep connections to localhost for MySQL
            " -t filter -A INPUT -i lo -j ACCEPT",
            " -t filter -A OUTPUT -o lo -j ACCEPT",

            // DEFAULT ROUTING POLICY
            " -t nat -P PREROUTING ACCEPT",
            " -t nat -P POSTROUTING ACCEPT",
            " -t nat -P OUTPUT ACCEPT",
            ];
        $return['rc'] = 0;


        foreach($rules as $rule) {
            $return = $this->RunCmd($this->bin_iptables." ".$rule, 'iptables');

            if($return['rc'] != 0) {
                echo "error on cli : ".$return['cmd']."\n";

                foreach($return['output'] as $line) {
                    echo "$line\n";
                }

                return $return['rc'];
            } 
        }
        return $return['rc'];
    }

    /**
     * This function flush iptables and set default policies to ACCEPT
     *
     */
    public function Stop() {
        parent::initialize();
        $rules = [
            " -t filter -F",
            " -t filter -X",
            " -t nat -F",
            " -t nat -X",
            // DEFAULT FILTER
            " -t filter -P INPUT ACCEPT",
            " -t filter -P FORWARD ACCEPT",
            " -t filter -P OUTPUT ACCEPT",

            // DEFAULT ROUTING
            " -t nat -P PREROUTING ACCEPT",
            " -t nat -P POSTROUTING ACCEPT",
            " -t nat -P OUTPUT ACCEPT",
            ];
        $return['rc'] = 0;

        foreach($rules as $rule) {
            $return = $this->RunCmd($this->bin_iptables." ".$rule, 'iptables');
            if($return['rc'] != 0) {
                echo "error on cli : ".$return['cmd']."\n";
                foreach($return['output'] as $line) {
                    echo "$line\n";
                }
                return $return['rc'];
            } 
        }
        return $return['rc'];
    }

    /**
     * This function create a new chain in iptables 
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to verify rule
     * 
     * @return integer: return code of command line
     */
    public function CreateChain($table, $chain)
    {
        parent::initialize();
        //$cmd = "$this->bin_sudo $this->bin_iptables -t $table -N $chain";
        $cmd = "$this->bin_iptables -t $table -N $chain";
        $return = $this->RunCmd($cmd, 'iptables');
        return($return['rc']);
    }

    /**
     * This function create a new chain in iptables 
     *
     * @param table : define filter or nat table
     * @param chain : the chain to load
     * @param builtin : Built-in iptables chain (e.g. INPUT, OUTPUT, PREROUTING ...)
     * @param src : source IP or source MAC option of iptable (e.g. "-s 192.168.1.8" or "-m mac --mac-source 00:00:00:00:00:00")
     * @param daysofweek : Days of week in Squid format (MTWHFAS)
     * @param timerange : Time range in Squid format (e.g. 08:00-18:00)
     * 
     * @return integer: return code of command line
     */
    public function LoadChain($table, $chain, $builtin, $src, $daysofweek=null, $timerange=null)
    {
        parent::initialize();

        if(isset($daysofweek) and isset($timerange))
        {
            /* build condition time rule */

            $this->loadModel('Config');
            $timezone = $this->Config->get('host_timezone');
            $timezone = $timezone['value'];

            $daysofweek = str_split($daysofweek);
    
            $timerange = explode('-', $timerange);
            $timestart = $timerange['0'];
            $timestop = $timerange['1'];
    
            $days = null;
            foreach($daysofweek as $day) {
                    if($day == 'M') { $wday = 'Mon';}
                elseif($day == 'T') { $wday = 'Tue';}
                elseif($day == 'W') { $wday = 'Wed';}
                elseif($day == 'H') { $wday = 'Thu';}
                elseif($day == 'F') { $wday = 'Fri';}
                elseif($day == 'A') { $wday = 'Sat';}
                elseif($day == 'S') { $wday = 'Sun';}
    
                // The day of week may change when convert time to UTC, 
                // we convert here day of week to UTC
                $gettimestart = new Time("$wday $timestart", $timezone);
                $gettimestart->timezone('utc');
                $days .= $gettimestart->format('D'.',');
                
            }
        
            $gettimestart = new Time($timestart, $timezone);
            $gettimestop = new Time($timestop, $timezone);
    
            $gettimestart->timezone('utc');
            $gettimestop->timezone('utc');
    
            $timestart = $gettimestart->format('H'.':'.'i');
            $timestop = $gettimestop->format('H'.':'.'i');

            $rule = "$src -m time --utc --timestart $timestart --timestop $timestop --weekdays $days -j $chain";
        } else {
            /* build rule without condition time */
            $rule = "$src -j $chain";
        }

        /* Verfy if chain is already loaded */
        $return = $this->VerifyRule($table, $builtin, $rule);

        if($return != 0) {
            /* if not loaded */
            $return = $this->RunCmd("$this->bin_iptables -t $table -I $builtin 1 $rule", 'iptables');
            return($return['rc']);
        } else {
            /* if loaded */
            return(1);
        }
    }

    /**
     * This function unload all chains found in iptables that matching $chains
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to verify rule
     * @param builtin : Built-in iptables chain (e.g. INPUT, OUTPUT, PREROUTING ...)
     * 
     * @return integer: return code of command line
     */
    public function UnloadChain($table, $chain, $builtin, $src_ip=null, $src_mac=null)
    {
        if(!isset($table) or !isset($chain) or !isset($builtin)) {
            $this->out('missing arguments');
            return(1);
        }
        parent::initialize();
        if(isset($src_ip)) {
            $return = $this->FindRules($table, [
                'chain' => $builtin,
                'target' => $chain,
                'src_ip' => $src_ip,
                ]);
        } elseif(isset($src_mac)) {
            $return = $this->FindRules($table, [
                'chain' => $builtin,
                'target' => $chain,
                'src_mac' => $src_mac,
                ]);
        } else {
            $return = $this->FindRules($table, [
                'chain' => $builtin,
                'target' => $chain,
                ]);
        }

        // Replacement conditions to convert "Add" rule to "Delete" rule
        $pattern = '/^\-A/';
        $replace = '-D';

        // Global return code
        $grc = 0;

        foreach($return['output'] as $rule) {
            /* Convert Add rule to Delete rule */
            $rule = preg_replace($pattern, $replace, $rule);
            //$cmd = "$this->bin_sudo $this->bin_iptables -t $table $rule";
            $cmd = "$this->bin_iptables -t $table $rule";
            /* exec rule */
            $return = $this->RunCmd($cmd, 'iptables'); 
            if($grc == 0 and $return['rc'] != 0) {
                $grc = 1;
            }
        }
        return($grc);
    }

    /**
     * This function remove all rules inside a chain
     * It use to reset rules when changed made in the profile
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to verify rule
     * 
     * @return integer: return code of command line
     */
    public function FlushChain($table, $chain)
    {
        parent::initialize();
        //$cmd1 = "$this->bin_sudo $this->bin_iptables -t $table -F $chain";
        $cmd1 = "$this->bin_iptables -t $table -F $chain";
        $return = $this->RunCmd($cmd1, 'iptables');
        return $return['rc'];
    }

    /**
     * This function delete chain. chain must be unload. 
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to verify rule
     * 
     * @return integer: return code of command line
     */
    public function DeleteChain($table, $chain)
    {
        parent::initialize();
        //$cmd1 = "$this->bin_sudo $this->bin_iptables -t $table -F $chain";
        $cmd1 = "$this->bin_iptables -t $table -F $chain";
        //$cmd2 = "$this->bin_sudo $this->bin_iptables -t $table -X $chain";
        $cmd2 = "$this->bin_iptables -t $table -X $chain";
        $return1 = $this->RunCmd($cmd1, 'iptables');
        $return2 = $this->RunCmd($cmd2, 'iptables');
        if($return1['rc'] == 0 and $return2['rc'] == 0) {
            return(0);
        } else {
            return(1);
        }
    }

    /**
     * This function List rules of specify chain
     *
     * @param table : define filter or nat table
     * @param chain : the chain where to add rule
     * 
     * @return array with output of all rules loaded
     */
    public function ListRules($table, $chain)
    {
        parent::initialize();
        //$cmd = "$this->bin_sudo $this->bin_iptables -t $table -vn -L $chain";
        $cmd = "$this->bin_iptables -t $table -vn -L $chain";
        $return = $this->RunCmd($cmd);
        foreach($return['output'] as $rule) {
            echo $rule."\n";
        }
        return($return['output']);
    }

    /**
     * This function add rule in a chain.
     *
     * @param table : define filter or nat table
     * @param chain : the chain where to add rule
     * @param rule : rule to add
     * 
     * @return integer: return code of command line
     */
    public function AddRule($table, $chain, $rule)
    {
        parent::initialize();
        $rc = 0;
        if(isset($rule)) {
            //$return = $this->VerifyRule($table, $chain, "-i $this->host_interface -o $this->host_interface $rule");
            $return = $this->VerifyRule($table, $chain, $rule);
            if($return != 0) {
                $cmd = "$this->bin_iptables -t $table -A $chain $rule";
                $return_var = $this->RunCmd($cmd, 'iptables');
                $rc = $return_var['rc'];
            }
        } else {
            $rc = 1;
        }
        return $rc;
    }

    /**
     * This function insert rule in a chain.
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to insert rule
     * @param rule : rule to add must begin by rule number
     * @param position (int) : position where put the rule, the rule will be on top (position 1)
     * 
     * @return integer: return code of command line
     */
    public function InsertRule($table, $chain, $rule, $position = null)
    {
        parent::initialize();
        $rc = 0;
        if(isset($rule)) {
            //$return = $this->VerifyRule($table, $chain, "-i $this->host_interface -o $this->host_interface $rule");
            $return = $this->VerifyRule($table, $chain, $rule);
            if($return != 0) {
                if(!isset($position)) {
                    $position = 1;
                }
                $cmd = "$this->bin_iptables -t $table -I $chain $position $rule";
                $return_var = $this->RunCmd($cmd, 'iptables');
                $rc = $return_var['rc'];
            }
        } else {
            $rc = 1;
        }
        return $rc;
    }

    /**
     * This function delete rule from a chain.
     *
     * @param table : define filter or nat table
     * @param chain : the chaine where to delete rule
     * @param rule : rule to delete
     * 
     * @return integer: return code of command line
     */
    public function DeleteRule($table, $chain, $rule)
    {
        parent::initialize();
        if(isset($rules)) {
            //$cmd = "$this->bin_sudo $this->bin_iptables -t $table -D $chain -i $this->host_interface -o $this->host_interface $rule";
            //$cmd = "$this->bin_sudo $this->bin_iptables -t $table -D $chain -i $this->host_interface $rule";
            $cmd = "$this->bin_iptables -t $table -D $chain $rule";
            $return_var = $this->RunCmd($cmd, 'iptables');
            return($return_var['rc']);
        }
    }

    /**
     * This function delete found rules
     *
     * @param table : define filter or nat table
     * @param conditions(Array) :  Same conditions as FindRules()
     *
     * @return integer: return code of command line
     */
    public function FindAndDeleteRules($table, $conditions=null)
    {
        parent::initialize();
        // Get Loaded chains that match $chain iand get them as iptables CLI
        $return = $this->FindRules($table, $conditions);

        // Replacement conditions to convert "Add" rule to "Delete" rule
        $pattern = '/^\-A/';
        $replace = '-D';

        // Global return code
        $grc = 0;

        foreach($return['output'] as $rule) {
            /* Convert Add rule to Delete rule */
            $rule = preg_replace($pattern, $replace, $rule);
            //$cmd = "$this->bin_sudo $this->bin_iptables -t $table $rule";
            $cmd = "$this->bin_iptables -t $table $rule";
            /* exec rule */
            $rc = $this->RunCmd($cmd, 'iptables'); 
            if($grc == 0 and $rc['rc'] != 0) {
                $grc = 1;
            }
        }
        return($grc);
    }

    /**
     * This function is shortcut of CreateChain() and LoadChain()
     *
     * @param table : define filter or nat table
     * @param chain : the chain to create and load
     *
     */
    public function CreateAndLoadChain($table, $chain)
    {
        parent::initialize();
        $this->CreateChain($table, $chain);
        $this->LoadChain($table, $chain);
    }

    /**
     * This function is shortcut of UnloadChain() and DeleteChain()
     *
     * @param table : define filter or nat table
     * @param chain : the chain to unload and delete
     *
     */
    public function UnloadAndDeleteChain($table, $chain, $builtin)
    {
        parent::initialize();
        $this->UnloadChain($table, $chain, $builtin);
        $this->DeleteChain($table, $chain);
    }
}
