<?php
/**
 * MyBB-Tags 2 Pacote de Língua Brasileira
 * Direitos Autorais 2014 My-BB.Ir Group, Todos os DIreitos Reservados
 * Traduzido por ArnoldLayne-xXx dthiago http://bf4brasil.com.br/
 * Author: AliReza_Tofighi - http://my-bb.ir
 *
 */
$l['tags_pluginname'] = "Etiquetas";

// Settings
$l['setting_group_tags'] = 'Plugin de Etiquetas';
$l['setting_group_tags_desc'] = "Configurações para Etiquetas.";

$l['setting_tags_enabled'] = "Habilitar plugin?";
$l['setting_tags_enabled_desc'] = 'Coloque "on" se deseja habilitar o plugin.';
$l['setting_tags_seo'] = "URL amigável";
$l['setting_tags_seo_desc'] = 'Você deseja habilitar URL´s amigáveis (ex: etiquetas-***.html) para etiquetas?<br />
Você deve adicionar o seguinte código ao seu arquivo ".htaccess" antes de habilitar o plugin:
<pre style="background: #f7f7f7;border: 1px solid #ccc;padding: 6px;border-radius: 3px;direction: ltr;text-align: left;font-size: 12px;">
RewriteEngine <strong>on</strong>
RewriteRule <strong>^tag-(.*?)\.html$ tag.php?name=$1</strong> <em>[L,QSA]</em>
RewriteRule <strong>^tag\.html$ tag.php</strong> <em>[L,QSA]</em>
</pre>';
$l['setting_tags_per_page'] = "Etiquetas por páginas";
$l['setting_tags_per_page_desc'] = 'Quantas etiquetas devem ser mostradas por páginas de "Etiquetas"?';
$l['setting_tags_limit'] = 'Limitar etiquetas na  "Index Page" e "Forum Display Page"';
$l['setting_tags_limit_desc'] = 'Quantas etiquetas devem ser mostradas na "Index Page" e "Forum Display Page"?';
$l['setting_tags_index'] = 'Mostrar etiquetas na página principal?';
$l['setting_tags_index_desc'] = 'Você deseja mostrar etiquetas na página principal?';
$l['setting_tags_forumdisplay'] = 'Mostrar etiquetas em "Forum Display" ?';
$l['setting_tags_forumdisplay_desc'] = 'Você deseja mostrar etiquetas na página  "Forum Display"?';
$l['setting_tags_max_thread'] = 'Máximo de Etiquetas por tópico';
$l['setting_tags_max_thread_desc'] = 'Insira o máximo de etiquetas pr tópico. Coloque 0 para ilimitado.';
$l['setting_tags_groups'] = 'Moderadores de Etiquetas';
$l['setting_tags_groups_desc'] = 'Selecione o grupo que pode editar etiquetas,observe, quem pode editar etiquetas pode editar tópicos.';
$l['setting_tags_bad'] = 'Etiquetas Proibidas';
$l['setting_tags_bad_desc'] = 'Adicione as etiquetas proibidas, estas etiquetas não serão mostradas na lista.Adicione uma etique por linha';
$l['setting_tags_droptable'] = 'Remover Tabelas?';
$l['setting_tags_droptable_desc'] = 'Você deseja que as "etiquetas" sejam removidas na desinstalação do plugin?';
$l['setting_tags_maxchars'] = 'Tamanho Máximo da Etiqueta';
$l['setting_tags_maxchars_desc'] = 'Insira o tamanho máximo que uma etiqueta pode ter';
$l['setting_tags_minchars'] = 'Tamanho Mínimo da Etiqueta';
$l['setting_tags_minchars_desc'] = 'Insira o tamanho mínimo que uma etiqueta pode ter';