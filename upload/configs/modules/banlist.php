<?php
$cfg = array (
	'MOD_ENABLE' => true, 
	'MOD_TITLE' => 'Бан-лист',
	'MOD_DESC' => 'Список забаненных игроков на ваших серверах.',
	'MOD_AUTHOR' => 'cwarwik',
	'MOD_SITE' => 'http://webmcr.iuguwsio.fun',
	'MOD_EMAIL' => 'ilya.bnck@gmail.com',
	'MOD_VERSION' => '1.0',
	'MOD_URL_UPDATE' => 'https://github.com/cwarwik/WebMCR-Reloaded-BanList',
	'MOD_CHECK_UPDATE' => false,
	'PAGINATION' => 10,
	'MOD_SETTING' => array (
		'PLUGIN' => 'advancedbans', // Используемый плагин (Сейчас не используется) (Модуль поддерживает: AdvancedBans)
		'TABLE' => 'punishments', // Таблица менеджера наказаний.
		'ID' => 'id', // Таблица с ID.
		'LOGIN' => 'name', // Таблица с никнеймом наказанного.
		'REASON' => 'reason', // Таблица с причиной наказания.
		'ADMIN' => 'operator', // Таблица с никнеймом администратора/модератора и т.д.
		'TYPE' => 'punishmentType', // Таблица с типом наказания.
		'TIME_START' => 'start', // Таблица с временем наказания.
		'TIME_END' => 'end', // Таблица с временем окончания наказания.
	)
);
?>