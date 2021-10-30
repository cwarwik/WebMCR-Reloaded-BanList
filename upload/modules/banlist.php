<?php

if(!defined("MCR")){ exit("Hacking Attempt!"); }

class module{
	private $core, $db, $cfg, $user, $lng;

	public function __construct($core){
		$this->core		= $core;
		$this->db		= $core->db;
        $this->cfg		= $core->cfg;
        $this->cfg_m    = $core->cfg_m;
		$this->user		= $core->user;
		$this->lng		= $core->lng_m;

		$bc = array(
			$this->lng['mod_name'] => BASE_URL."?mode=banlist"
		);
		
		$this->core->bc = $this->core->gen_bc($bc);
    }
    
    private function banlist_array() {
        ob_start();

        $start = $this->core->pagination($this->cfg_m['PAGINATION'], 0, 0);
        $end = $this->cfg_m['PAGINATION'];

        $query = $this->db->query("SELECT `{$this->cfg_m['MOD_SETTING']['ID']}`, `{$this->cfg_m['MOD_SETTING']['LOGIN']}`, `{$this->cfg_m['MOD_SETTING']['ADMIN']}`,
										`{$this->cfg_m['MOD_SETTING']['REASON']}`, `{$this->cfg_m['MOD_SETTING']['TIME_START']}`, `{$this->cfg_m['MOD_SETTING']['TIME_END']}`,
										`{$this->cfg_m['MOD_SETTING']['TYPE']}`
									FROM `{$this->cfg_m['MOD_SETTING']['TABLE']}`
									ORDER BY `{$this->cfg_m['MOD_SETTING']['ID']}` DESC
									LIMIT $start,$end");

        if(!$query || $this->db->num_rows($query)<=0){ echo $this->core->sp(MCR_THEME_MOD."banlist/banlist-none.html"); return ob_get_clean(); } // Check returned result

        while($ar = $this->db->fetch_assoc($query)){

            $type = $this->db->HSC($ar[$this->cfg_m['MOD_SETTING']['TYPE']]);
            $timeend = $this->db->HSC($ar[$this->cfg_m['MOD_SETTING']['TIME_END']]);
            
            switch($type) {
                case 'BAN': $type = $this->lng['type_ban'];
                break;
                case 'TEMP_BAN': $type = $this->lng['type_tempban'];
                break;
                case 'MUTE': $type = $this->lng['type_mute'];
                break;
                case 'TEMP_MUTE': $type = $this->lng['type_tempmute'];
                break;
                case 'WARNING': $type = $this->lng['type_warn'];
                break;
                case 'TEMP_WARNING': $type = $this->lng['type_tempwarn'];
                break;
            }

            ($timeend == -1) ? $timeend = $this->lng['time_pernament'] : $timeend = date("d.m.Y в H:i", $ar[$this->cfg_m['MOD_SETTING']['TIME_END']]/1000);

			$data = array(
				'ID' => intval($ar[$this->cfg_m['MOD_SETTING']['ID']]),
				'LOGIN' => $this->db->HSC($ar[$this->cfg_m['MOD_SETTING']['LOGIN']]),
				'ADMIN' => $this->db->HSC($ar[$this->cfg_m['MOD_SETTING']['ADMIN']]),
				'REASON' => ($this->db->HSC($ar[$this->cfg_m['MOD_SETTING']['REASON']]) == 'none') ? $this->lng['noreason'] : $this->db->HSC($ar[$this->cfg_m['MOD_SETTING']['REASON']]),
				'TIME_START' => date("d.m.Y в H:i", $ar[$this->cfg_m['MOD_SETTING']['TIME_START']]/1000),
				'TIME_END' => $timeend,
				'TYPE' => $type
            );
            
			echo $this->core->sp(MCR_THEME_MOD."banlist/banlist-users.html", $data);
        }

        return ob_get_clean();
    }

    private function banlist_stutus() {
        ob_start();
        $query = $this->db->query("SELECT * FROM `{$this->cfg_m['MOD_SETTING']['TABLE']}` WHERE `{$this->cfg_m['MOD_SETTING']['LOGIN']}`='".$this->user->login."'");
		
        if ($this->db->num_rows($query) > 0) {
            echo $this->core->sp(MCR_THEME_MOD."banlist/alerts/yesban.html");
        }else {
            echo $this->core->sp(MCR_THEME_MOD."banlist/alerts/noban.html");
        }
        return ob_get_clean();
    }

	public function content(){

        $sql	= "SELECT COUNT(*) FROM `{$this->cfg_m['MOD_SETTING']['TABLE']}`";
        $page	= "?mode=banlist&pid=";
        $query = $this->db->query($sql);

        if(!$query){ 
            exit("SQL Error"); 
        }

        $ar = $this->db->fetch_array($query);
        
        $data = array(
            "STATUS" => $this->banlist_stutus(),
			"PAGINATION" => $this->core->pagination($this->cfg_m['PAGINATION'], $page, $ar[0]),
            "CONTENT" => $this->banlist_array(),
        );
        
        $this->core->header = $this->core->sp(MCR_THEME_MOD."banlist/header.html");
        return $this->core->sp(MCR_THEME_MOD."banlist/banlist-full.html", $data);
        
	}
}

?>