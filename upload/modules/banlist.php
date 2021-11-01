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
			$this->lng['mod_name'] => BASE_URL."?mode=clans"
		);
		
		$this->core->bc = $this->core->gen_bc($bc);
    }

	// Топ кланов
	private function clansTop() {
		ob_start();

		$start = $this->core->pagination($this->cfg_m['PAGINATION'], 0, 0);
        $end = $this->cfg_m['PAGINATION'];

		$query = $this->db->query("SELECT *, COUNT(*) AS tagcount, {$this->cfg_m['MOD_SETTING']['TABLE_CLANS']}.name AS nclans FROM `{$this->cfg_m['MOD_SETTING']['TABLE_CLANS']}` LEFT JOIN `{$this->cfg_m['MOD_SETTING']['TABLE_PLAYERS']}` ON {$this->cfg_m['MOD_SETTING']['TABLE_CLANS']}.tag = {$this->cfg_m['MOD_SETTING']['TABLE_PLAYERS']}.tag LIMIT $start, $end");

		if (!$query || $this->db->num_rows($query)<=0) {
			echo $this->core->sp(MCR_THEME_MOD."clans/clans-none.html");
			return ob_get_clean();
		}

		while ($ar = $this->db->fetch_assoc($query)) {
			$data = array(
				'ID' => intval($ar['id']),
				'TAG' => $this->db->HSC($ar['tag']),
				'NAME' => $this->db->HSC($ar['nclans']),
				'COUNT' => $this->db->HSC($ar['tagcount']),
				'BALANCE' => $this->db->HSC($ar['balance']),
			);

			echo $this->core->sp(MCR_THEME_MOD."clans/clans-list.html", $data);
		}

		return ob_get_clean();
	}

	// Топ игроков
	private function playersTop() {
		ob_start();

		$start = $this->core->pagination($this->cfg_m['PAGINATION'], 0, 0);
        $end = $this->cfg_m['PAGINATION'];

		$query = $this->db->query("SELECT * FROM `{$this->cfg_m['MOD_SETTING']['TABLE_PLAYERS']}` ORDER BY `id` DESC LIMIT $start, $end");

		if (!$query || $this->db->num_rows($query)<=0) {
			echo $this->core->sp(MCR_THEME_MOD."clans/clans-none.html");
			return ob_get_clean();
		}

		while ($ar = $this->db->fetch_assoc($query)) {
			$data = array(
				'ID' => intval($ar['id']),
				'TAG' => $this->db->HSC($ar['tag']),
				'NAME' => $this->db->HSC($ar['name']),
				'KILL' => $this->db->HSC($ar['neutral_kills'] + $ar['rival_kills'] + $ar['civilian_kills'] + $ar['ally_kills']),
				'DEATHS' => $this->db->HSC($ar['deaths']),
				'LAST_SEEN' => date("d.m.Y в H:i", $ar['last_seen']/1000),
			);

			echo $this->core->sp(MCR_THEME_MOD."clans/clans-players.html", $data);
		}
		return ob_get_clean();
	}

	public function content(){

        $page	= "?mode=clans&pid=";
        $query = $this->db->query("SELECT COUNT(*) FROM `{$this->cfg_m['MOD_SETTING']['TABLE_CLANS']}`");
        
        if(!$query){exit("SQL Error");}

        $ar = $this->db->fetch_array($query);
        
        $data = array(
			"PAGINATION" => $this->core->pagination($this->cfg_m['PAGINATION'], $page, $ar[0]),
            "TOP" => $this->clansTop(),
			"PLAYERS" => $this->playersTop(),
        );
        
        $this->core->header = $this->core->sp(MCR_THEME_MOD."clans/header.html");
        return $this->core->sp(MCR_THEME_MOD."clans/clans-full.html", $data);
        
	}
}

?>
