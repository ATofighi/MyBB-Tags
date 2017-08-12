<?php
function tags_install()
{
	global $db, $lang, $mybb;

	$db->delete_query('templates', "title LIKE 'tags\_%' AND sid='-1'");
	$db->delete_query('settings', "name LIKE 'tags\_%'");

	$templatearray = array(
		array(
			"title" => 'tags_input',
			"template" => $db->escape_string('<tr>
	<td class="trow2" width="20%" valign="top"><strong>{$lang->tags}:</strong></td>
	<td class="trow2"><input type="hidden" id="tags"  />
		<div id="tags-inputs">{$tagInputs}</div></td>
</tr>
<link rel="stylesheet" href="{$mybb->asset_url}/jscripts/select2/select2.css?ver=1807">
<script type="text/javascript" src="{$mybb->asset_url}/jscripts/select2/select2.min.js?ver=1806"></script>

<script type="text/javascript">
MyBB.select2();
$("#tags").select2({
	createSearchChoice:function(term, data) {
		if($.trim(term)) {
			return {id:term, text:term}
		}
	},
	initSelection : function (element, callback) {
		var vals = element.select2(\'val\');
		var data = [];
		for(var i in vals) {
			var val = vals[i];
			data.push({id: val, text: val});
		}
        callback(data);
    },
    multiple: true,
	width: \'100%\',
	data: {$tagsData}
}).change(function(data){
	$(\'#tags-inputs\').html(\'\');
	for(val in data.val) {
		var input = $(\'<input />\');
		input.attr(\'type\', \'hidden\');
		input.attr(\'name\', \'tags[]\');
		input.val(data.val[val]);
		$(\'#tags-inputs\').append(input);
	}
});
$(\'input[name=subject]\').blur(function(){
	var txt = $(this).val();
	txt = $.trim(txt.replace(/([ +"\'\\\\/\\]\\[,.-])/gi, \' \'));
	var tags = txt.split(\' \');
	var val = $(\'#tags\').select2(\'val\');
	for(var i in tags) {
		var tag = tags[i];
		val.push(tag);
	}
	var data = [];
	for(var i in val) {
		var tag = val[i];
		data.push({
			id: tag,
			text: tag
		});
	}
	$(\'#tags\').select2(\'val\', val);
});
$("#tags").select2(\'val\', {$tagsJson});
</script>'),
			"sid" => "-1"
		),
			array(
				"title" => 'tags_input_hidden',
				"template" => $db->escape_string('<input type="hidden" name="tags[]" value="{$tag}" />'),
				"sid" => "-1"
		),
		array(
			"title" => 'tags_box',
			"template" => $db->escape_string('<br class="clear" />
<style type="text/css">
.tag {
	display: inline-block;
	vertical-align: middle;
	box-sizing: content-box;
	word-wrap: normal;
	word-spacing: normal;
	position: relative;
	height: 24px;
	font-size: 11px;
	padding:0 10px 0 12px;
	background:#0089e0;
	text-shadow: -1px -1px 3px #555;
	color:#fff;
	text-decoration:none;
	-moz-border-radius-bottomright:4px;
	-webkit-border-bottom-right-radius:4px;
	border-bottom-right-radius:4px;
	-moz-border-radius-topright:4px;
	-webkit-border-top-right-radius:4px;
	border-top-right-radius:4px;
}

.tag:link, .tag:hover, .tag:visited, .tag:active {
	color:#fff;
	text-decoration: none;
}

.tag:before{
	content:"";
	float:left;
	position:absolute;
	top:0;
	right: 100%;
	width:0;
	height:0;
	border-color:transparent #0089e0 transparent transparent;
	border-style:solid;
	border-width:12px 12px 12px 0;
}

.tag:after{
	content:"";
	position:absolute;
	top:50%;
	left:0;
	margin-top:-2px;
	float:left;
	width:4px;
	height:4px;
	-moz-border-radius:2px;
	-webkit-border-radius:2px;
	border-radius:2px;
	background:#fff;
	-moz-box-shadow:-1px -1px 2px #004977;
	-webkit-box-shadow:-1px -1px 2px #004977;
	box-shadow:-1px -1px 2px #004977;
}

.tag:hover{
	background:#555;
}

.tag:hover:before{
	border-color: transparent #555 transparent transparent;
}

.tag.tag-h1 {
	font-size: 32px;
	height: 42px;
	margin-left: 21px;
}

.tag.tag-h1:before {
	border-width: 21px;
	border-left-width:0;
}

.tag.tag-h2 {
	font-size: 24px;
	height: 34px;
	margin-left: 17px;
}

.tag.tag-h2:before {
	border-width: 17px;
	border-left-width:0;
}

.tag.tag-h3 {
	font-size: 20px;
	height: 28px;
	margin-left: 14px;
}

.tag.tag-h3:before {
	border-width: 14px;
	border-left-width:0;
}

.tag.tag-h4 {
	font-size: 17px;
	height: 24px;
	margin-left: 12px;
}

.tag.tag-h4:before {
	border-width: 12px;
	border-left-width:0;
}

.tag.tag-h5 {
	font-size: 14px;
	height: 20px;
	margin-left:10px;
}

.tag.tag-h5:before {
	border-width: 10px;
	border-left-width:0;
}

.tag.tag-h6 {
	font-size: 11px;
	height: 16px;
	margin-left:8px
}

.tag.tag-h6:before {
	border-width: 8px;
	border-left-width:0;
}
</style>
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder tfixed clear">
	<thead>
	<tr>
		<td class="thead">
			<div class="expcolimage"><img src="{$theme[\'imgdir\']}/collapse{$collapsedimg[\'tags\']}.png" id="tags_img" class="expander" alt="[-]" title="[-]" /></div>
			<strong>{$lang->tags}</strong>
		</td>
	</tr>
	</thead>
	<tbody style="{$collapsed[\'tags_e\']}" id="tags_e">
	<tr>
		<td class="trow1">
			{$tags}
		</td>
	</tr>
	</tbody>
</table>
<br class="clear" />
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_box_tag',
			"template" => $db->escape_string('
 <a href="{$mybb->settings[\'bburl\']}/{$tag_link}" title="{$tag}" class="tag tag-h5">{$tag}</a>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_box_tag_sized',
			"template" => $db->escape_string(' <a href="{$tag[\'tag_link\']}" class="tag tag-h{$tag[\'size\']}">{$tag[\'name\']}</a>'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_search',
			"template" => $db->escape_string('<html>
		<head>
			<title>{$mybb->settings[\'bbname\']} - {$lang->tags}</title>
			{$headerinclude}
		</head>
		<body>
			{$header}
			<form action="{$tag_link}" method="get">
			<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder clear">
				<tr>
					<td class="thead" colspan="2">
						<strong>{$lang->tags_search}</strong>
					</td>
				</tr>
				<tr>
					<td class="trow2">
						<input type="text" class="textbox" placeholder="{$lang->tags_search_placeholder}" name="name" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" />
					</td>
					<td class="trow2" width="50">
						<input type="submit" class="button" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="{$lang->tags_go}" />
					</td>
				</tr>
			</table>
			</form>
			{$footer}
		</body>
	</html>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_thread',
			"template" => $db->escape_string('
	<tr>
		<td class="tcat" colspan="2">
			<div class="float_{$no_dir}">
				{$lang->tags_author}: <strong>{$tag[\'profilelink\']}</strong> - {$lang->tags_replies}: <a href="javascript:MyBB.whoPosted({$tag[\'tid\']});">{$tag[\'replies\']}</a> - {$lang->tags_views}: {$tag[\'views\']}
			</div>
			<a href="{$tag[\'threadlink\']}{$highlight}"><strong>{$tag[\'subject\']}</strong></a>
		</td>
	</tr>
	<tr>
		<td class="trow1" colspan="2">
			<div style="max-height:100px;overflow:auto">
				{$tag[\'message\']}
			</div>
		</td>
	</tr>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_notags',
			"template" => $db->escape_string('
<tr>
	<td class="trow1" colspan="2">
		{$lang->tags_notags}
	</td>
</tr>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_viewtag',
			"template" => $db->escape_string('
	<html>
		<head>
			<title>{$lang->tags} - {$name}</title>
			{$headerinclude}
		</head>
		<body>
			{$header}
			<form action="{$tag_link}" method="get">
			<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder clear">
				<tr>
					<td class="thead" colspan="2">
						<strong>{$lang->tags}</strong>
					</td>
				</tr>
				<tr>
					<td class="trow2">
						<input type="text" class="textbox" name="name" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="{$name}" />
					</td>
					<td class="trow2" width="50">
						<input type="submit" class="button" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="{$lang->tags_go}" />
					</td>
				</tr>
				{$tags}
			</table>
			</form>
			{$multipage}
			{$footer}
		</body>
	</html>
'),
			"sid" => "-1"
		)
	);

	$db->insert_query_multiple("templates", $templatearray);

	// create settings group
	$insertarray = array(
		'name' => 'tags',
		'title' => 'Tags Plugin',
		'description' => "Settings for Tags Plugin.",
		'disporder' => 100,
		'isdefault' => 0
	);
	$gid = $db->insert_query("settinggroups", $insertarray);

	// Create our entries table
	$collation = $db->build_create_table_collation();

	if(!$db->table_exists('tags'))
	{
		if($db->type == 'pgsql')
		{
			$db->write_query("CREATE TABLE `".TABLE_PREFIX."tags` (
					`id` serial,
					`tid` int NOT NULL default '0',
					`name` varchar(200)  NOT NULL default '',
					`hash` varchar(200)  NOT NULL default '',
					PRIMARY KEY  (`id`)
				) ENGINE=MyISAM{$collation}");
		}
		else
		{
			$db->write_query("CREATE TABLE `".TABLE_PREFIX."tags` (
					`id` int(10) UNSIGNED NOT NULL auto_increment,
					`tid` int(100) UNSIGNED NOT NULL default '0',
					`name` varchar(200)  NOT NULL default '',
					`hash` varchar(200)  NOT NULL default '',
					PRIMARY KEY  (`id`)
				) ENGINE=MyISAM{$collation}");
		}
	}

	if(!$db->table_exists('tags_slug'))
	{
		if($db->type == 'pgsql')
		{
			$db->write_query("CREATE TABLE `".TABLE_PREFIX."tags_slug` (
					`name` varchar(200)  NOT NULL default '',
					`slug` varchar(200)  NOT NULL default '',
					`count` int NOT NULL default '0',
					UNIQUE KEY (`name`),
					UNIQUE KEY (`slug`)
				) ENGINE=MyISAM{$collation}");
		}
		else
		{
			$db->write_query("CREATE TABLE `".TABLE_PREFIX."tags_slug` (
					`name` varchar(200)  NOT NULL default '',
					`slug` varchar(200)  NOT NULL default '',
					`count` int(10) UNSIGNED NOT NULL default '0',
					UNIQUE KEY (`name`),
					UNIQUE KEY (`slug`)
				) ENGINE=MyISAM{$collation}");
		}
	}
}
