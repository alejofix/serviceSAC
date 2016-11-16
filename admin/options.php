<?php

define('IN_SCRIPT',1);
define('HESK_PATH','../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');

$id     = hesk_input( hesk_GET('i') );
$query  = hesk_input( hesk_utf8_urldecode( hesk_GET('q') ) );
$type   = hesk_input( hesk_GET('t', 'text') );
$maxlen = intval( hesk_GET('m', 255) );
$query  = stripslashes($query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML; 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<title><?php echo $hesklang['opt']; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $hesklang['ENCODING']; ?>" />
<style type="text/css">
body
{
        margin:5px 5px;
        padding:0;
        background:#fff;
        color: black;
        font : 68.8%/1.5 Verdana, Geneva, Arial, Helvetica, sans-serif;
        text-align:left;
}

p
{
        color : black;
        font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
        font-size: 1.0em;
}
h3
{
        color : #AF0000;
        font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
        font-weight: bold;
        font-size: 1.0em;
        text-align:center;
}
.title
{
        color : black;
        font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
        font-weight: bold;
        font-size: 1.0em;
}
.wrong   {color : red;}
.correct {color : green;}
</style>
</head>
<body>

<h3><?php echo $hesklang['opt']; ?></h3>

<p><i><?php echo $hesklang['ns']; ?></i></p>

<?php

switch ($type)
{
	case 'text':
    	echo '
        <script language="javascript">
        function hesk_saveOptions()
        {
        	window.opener.document.getElementById(\'s_'.$id.'_val\').value = document.getElementById(\'o2\').value;
            window.opener.document.getElementById(\'s_'.$id.'_maxlen\').value = document.getElementById(\'o1\').value;
            window.close();
        }
        </script>
		<table border="0">
        <tr>
        <td>'.$hesklang['custom_l'].':<td>
        <td><input type="text" name="o1" id="o1" value="'.$maxlen.'" size="30" /></td>
        </tr>
        <tr>
        <td>'.$hesklang['defw'].':<td>
        <td><input type="text" name="o2" id="o2" value="'.$query.'" size="30" /></td>
        </tr>
        </table>
        <p><input type="button" value="  '.$hesklang['ok'].'  " onclick="Javascript:hesk_saveOptions()" /></p>
        ';
    	break;
    case 'textarea':
    	if (strpos($query,'#') !== false)
        {
        	list($rows,$cols)=explode('#',$query);
        }
        else
        {
        	$rows = '';
            $cols = '';
        }
    	echo '
        <script language="javascript">
        function hesk_saveOptions()
        {
        	window.opener.document.getElementById(\'s_'.$id.'_val\').value = document.getElementById(\'o1\').value + "#" + document.getElementById(\'o2\').value;
            window.close();
        }
        </script>
		<table border="0">
        <tr>
        <td>'.$hesklang['rows'].':<td>
        <td><input type="text" name="o1" id="o1" value="'.$rows.'" size="5" /></td>
        </tr>
        <tr>
        <td>'.$hesklang['cols'].':<td>
        <td><input type="text" name="o2" id="o2" value="'.$cols.'" size="5" /></td>
        </tr>
        </table>
        <p><input type="button" value="  '.$hesklang['ok'].'  " onclick="Javascript:hesk_saveOptions()" /></p>
        ';
    	break;
    case 'radio':
    	$options=str_replace('#HESK#',"\n",$query);
    	echo '
        <script language="javascript">
        function hesk_saveOptions()
        {
        	text = document.getElementById(\'o1\').value;
            text = text.replace(/^\s\s*/, \'\').replace(/\s\s*$/, \'\');
			text = escape(text);
			if(text.indexOf(\'%0D%0A\') > -1)
			{
				re_nlchar = /%0D%0A/g ;
			}
		    else if(text.indexOf(\'%0A\') > -1)
			{
				re_nlchar = /%0A/g ;
            }
				else if(text.indexOf(\'%0D\') > -1)
			{
				re_nlchar = /%0D/g ;
			}
            else
            {
            	alert(\''.addslashes($hesklang['atl2']).'\');
                return false;
            }
			text = unescape(text.replace(re_nlchar,\'#HESK#\'));

        	window.opener.document.getElementById(\'s_'.$id.'_val\').value = text;
            window.close();
        }
        </script>

        <p>'.$hesklang['opt2'].'</p>
        <textarea name="o1" id="o1" rows="6" cols="40">'.$options.'</textarea>
        <p><input type="button" value="  '.$hesklang['ok'].'  " onclick="Javascript:hesk_saveOptions()" /></p>
        ';
    	break;
    case 'select':

		$query = str_replace('{HESK_SELECT}', '', $query, $show_select);

    	$options=str_replace('#HESK#',"\n",$query);
    	echo '
        <script language="javascript">
        function hesk_saveOptions()
        {
        	text = document.getElementById(\'o1\').value;
            text = text.replace(/^\s\s*/, \'\').replace(/\s\s*$/, \'\');
			text = escape(text);
			if(text.indexOf(\'%0D%0A\') > -1)
			{
				re_nlchar = /%0D%0A/g ;
			}
		    else if(text.indexOf(\'%0A\') > -1)
			{
				re_nlchar = /%0A/g ;
            }
			else if(text.indexOf(\'%0D\') > -1)
			{
				re_nlchar = /%0D/g ;
			}
            else
            {
            	alert(\''.addslashes($hesklang['atl2']).'\');
                return false;
            }
			text = unescape(text.replace(re_nlchar,\'#HESK#\'));

			if (document.getElementById(\'show_select\').checked)
			{
				text = "{HESK_SELECT}" + text;
			}

        	window.opener.document.getElementById(\'s_'.$id.'_val\').value = text;
            window.close();
        }
        </script>

        <p>'.$hesklang['opt3'].'</p>
		<p><label><input type="checkbox" name="show_select" id="show_select" value="1" ' . ($show_select ? 'checked="checked"' : '') . ' /> ' . $hesklang['show_select'] . '</label></p>
        <textarea name="o1" id="o1" rows="6" cols="40">'.$options.'</textarea>
        <p><input type="button" value="  '.$hesklang['ok'].'  " onclick="Javascript:hesk_saveOptions()" /></p>
        ';
    	break;
    case 'checkbox':
    	$options=str_replace('#HESK#',"\n",$query);
    	echo '
        <script language="javascript">
        function hesk_saveOptions()
        {
        	text = document.getElementById(\'o1\').value;
            text = text.replace(/^\s\s*/, \'\').replace(/\s\s*$/, \'\');
			text = escape(text);
			if(text.indexOf(\'%0D%0A\') > -1)
			{
				re_nlchar = /%0D%0A/g ;
			}
		    else if(text.indexOf(\'%0A\') > -1)
			{
				re_nlchar = /%0A/g ;
            }
			else if(text.indexOf(\'%0D\') > -1)
			{
				re_nlchar = /%0D/g ;
			}
            else
            {
            	alert(\''.addslashes($hesklang['atl2']).'\');
                return false;
            }
			text = unescape(text.replace(re_nlchar,\'#HESK#\'));

        	window.opener.document.getElementById(\'s_'.$id.'_val\').value = text;
            window.close();
        }
        </script>

        <p>'.$hesklang['opt4'].'</p>
        <textarea name="o1" id="o1" rows="6" cols="40">'.$options.'</textarea>
        <p><input type="button" value="  '.$hesklang['ok'].'  " onclick="Javascript:hesk_saveOptions()" /></p>
        ';
    	break;
    default:
    	die('Invalid type');
}
?>

<p align="center"><a href="#" onclick="Javascript:window.close()"><?php echo $hesklang['cwin']; ?></a></p>

<p>&nbsp;</p>

</body>

</html>
<?php
exit();
?>
