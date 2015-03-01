<?php
function tags_deactivate()
{
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets('newthread', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('editpost', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('showthread', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('showthread', '#'.preg_quote('<meta name="keywords" content="{$thread[\'tags_meta\']}" />').'#', '');
	find_replace_templatesets('index', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('forumdisplay', '#'.preg_quote('{$tags}').'#', '');
}