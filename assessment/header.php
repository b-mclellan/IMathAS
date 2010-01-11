<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo $installname;?> Assessment</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<?php
$start_time = microtime(true); 
//load filter
$curdir = rtrim(dirname(__FILE__), '/\\');
$loadgraphfilter = true;
require("$curdir/../filter/filter.php");
?>
<script type="text/javascript">
function init() {
	for (var i=0; i<initstack.length; i++) {
		var foo = initstack[i]();
	}
}
initstack = new Array();
window.onload = init;
var imasroot = '<?php echo $imasroot; ?>';
</script>
<link rel="stylesheet" href="<?php echo $imasroot . "/assessment/mathtest.css?ver=121809";?>" type="text/css"/>
<?php
echo "<script type=\"text/javascript\" src=\"$imasroot/javascript/general.js?ver=120209\"></script>\n";
if (isset($sessiondata['coursetheme'])) {
	if (isset($flexwidth)) {
		$coursetheme = str_replace('_fw','',$sessiondata['coursetheme']);
	} else {
		$coursetheme = $sessiondata['coursetheme'];
	}
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$imasroot/themes/$coursetheme\"/>\n";
}
if ($isdiag) {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$imasroot/diag/print.css\" media=\"print\"/>\n";
} else {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$imasroot/assessment/print.css\" media=\"print\"/>\n";
}
if ($sessiondata['mathdisp']==1) {
	echo "<script type=\"text/javascript\" src=\"$imasroot/javascript/ASCIIMathML_min.js\"></script>\n";
} else if ($sessiondata['mathdisp']==2) {
	echo '<script type="text/javascript">var AMTcgiloc = "'.$mathimgurl.'";</script>'; 
	echo "<script src=\"$imasroot/javascript/ASCIIMathTeXImg_min.js\" type=\"text/javascript\"></script>\n";
} else if ($sessiondata['mathdisp']==0) {
	echo '<script type="text/javascript">var noMathRender = true;</script>';	
}

if ($sessiondata['graphdisp']==1) {
	echo "<script src=\"$imasroot/javascript/ASCIIsvg_min.js\" type=\"text/javascript\"></script>\n";
} else {
	echo "<script src=\"$imasroot/javascript/mathjs.js\" type=\"text/javascript\"></script>\n";
}
?>
<script src="<?php echo $imasroot . "/javascript/AMhelpers_min.js?v=011010";?>" type="text/javascript"></script>
<script src="<?php echo $imasroot . "/javascript/confirmsubmit.js";?>" type="text/javascript"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo $imasroot;?>/javascript/excanvas_min.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo $imasroot;?>/javascript/drawing_min.js?v=121809"></script>
<?php
echo "<script type=\"text/javascript\">imasroot = '$imasroot';</script>";
if ($useeditor==1 && $sessiondata['useed']==1) {
echo <<<END
<script type="text/javascript" src="$imasroot/editor/tiny_mce.js"></script>

<script type="text/javascript">
tinyMCE.init({
    mode : "textareas",
    editor_selector : "mceEditor",
    theme : "advanced",
    theme_advanced_buttons1 : "fontselect,fontsizeselect,formatselect,bold,italic,underline,strikethrough,separator,sub,sup,separator,cut,copy,paste,pasteword,undo,redo",
    theme_advanced_buttons2 : "justifyleft,justifycenter,justifyright,justifyfull,separator,numlist,bullist,outdent,indent,separator,forecolor,backcolor,separator,hr,link,unlink,charmap,image,table,code,separator,asciimath,asciimathcharmap,asciisvg",
    theme_advanced_buttons3 : "",
    theme_advanced_fonts : "Arial=arial,helvetica,sans-serif,Courier New=courier new,courier,monospace,Georgia=georgia,times new roman,times,serif,Tahoma=tahoma,arial,helvetica,sans-serif,Times=times new roman,times,serif,Verdana=verdana,arial,helvetica,sans-serif",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    plugins : 'safari,asciimath,asciisvg,table,inlinepopups,paste',
    gecko_spellcheck : true,
    extended_valid_elements : '@[sscr]',
    theme_advanced_resizing : true,
    cleanup_callback : "imascleanup",
    AScgiloc : '$imasroot/filter/graph/svgimg.php',
    ASdloc : '$imasroot/javascript/d.svg'
END;
if (isset($AWSkey)) {
echo <<<END
    ,file_browser_callback : "fileBrowserCallBack"
});
function fileBrowserCallBack(field_name, url, type, win) {
	var connector = "$imasroot/editor/file_manager.php";
	my_field = field_name;
	my_win = win;
	switch (type) {
		case "image":
			connector += "?type=img";
			break;
		case "file":
			connector += "?type=files";
			break;
	}
	tinyMCE.activeEditor.windowManager.open({
		file : connector,
		title : 'File Browser',
		width : 450,  // Your dimensions may differ - toy around with them!
		height : 450,
		resizable : "yes",
		inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
		close_previous : "no"
	    }, {
		window : win,
		input : field_name
	    });

	//window.open(connector, "file_manager", "modal,width=450,height=440,scrollbars=1");
}

END;
} else {
	echo "});";
}
echo <<<END
function imascleanup(type, value) {
	if (type=="get_from_editor") {
		value = value.replace(/[\x84\x93\x94]/g,'"');
		var rl = '\u2122,<sup>TM</sup>,\u2026,...,\u201c|\u201d,",\u2018|\u2019,\',\u2013|\u2014|\u2015|\u2212,-'.split(',');
		for (var i=0; i<rl.length; i+=2) {
			value = value.replace(new RegExp(rl[i], 'gi'), rl[i+1]);
		}
		value = value.replace(/<!--([\s\S]*?)-->|&lt;!--([\s\S]*?)--&gt;|<style>[\s\S]*?<\/style>/g, "");  // Word comments
		value = value.replace(/class="?Mso\w+"?/g,'');
		value = value.replace(/<p\s*>\s*<\\/p>/gi,'');
	}
	return value;
}
</script>
<!-- /TinyMCE -->

END;

}
if (isset($placeinhead)) {
	echo $placeinhead;
}
echo '</head><body>';

$insertinheaderwrapper = ' ';
echo '<div class=mainbody>';
if (isset($insertinheaderwrapper)) {
	echo '<div class="headerwrapper">'.$insertinheaderwrapper.'</div>';
}
echo '<div class="midwrapper">';

?>

</head>
<body>
<div class=main>
