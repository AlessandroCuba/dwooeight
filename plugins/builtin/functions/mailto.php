<?php

/**
 * TOCOM
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * This file is released under the LGPL
 * "GNU Lesser General Public License"
 * More information can be found here:
 * {@link http://www.gnu.org/copyleft/lesser.html}
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://www.gnu.org/copyleft/lesser.html  GNU Lesser General Public License
 * @link       http://dwoo.org/
 * @version    0.3.3
 * @date       2008-03-19
 * @package    Dwoo
 */
function DwooPlugin_mailto(Dwoo $dwoo, $address, $text=null, $subject=null, $encode=null, $cc=null, $bcc=null, $newsgroups=null, $followupto=null, $extra=null)
{
	if(empty($address))
		return '';
	if(empty($text))
		$text = $address;

	// build address string
	$address .= '?';

	if(!empty($subject))
		$address .= 'subject='.rawurlencode($subject).'&';
	if(!empty($cc))
		$address .= 'cc='.rawurlencode($cc).'&';
	if(!empty($bcc))
		$address .= 'bcc='.rawurlencode($bcc).'&';
	if(!empty($newsgroup))
		$address .= 'newsgroups='.rawurlencode($newsgroups).'&';
	if(!empty($followupto))
		$address .= 'followupto='.rawurlencode($followupto).'&';

	$address = rtrim($address, '?&');

	// output
	switch($encode)
	{
		case 'none':
		case null:
			return '<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>';

		case 'js':
		case 'javascript':
			$str = 'document.write(\'<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>\');';
			$len = strlen($str);

			$out = '';
	        for ($i=0; $i<$len; $i++)
	            $out .= '%'.bin2hex($str[$i]);

	        return '<script type="text/javascript">eval(unescape(\''.$out.'\'));</script>';

			break;
		case 'javascript_charcode':
		case 'js_charcode':
		case 'jscharcode':
		case 'jschar':
	        $str = '<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>';
			$len = strlen($str);

	        $out = '<script type="text/javascript">'."\n<!--\ndocument.write(String.fromCharCode(";
	        for ($i=0; $i<$len; $i++)
	            $out .= ord($str[$i]).',';
	        return rtrim($out, ',') . "));\n-->\n</script>\n";

			break;

		case 'hex':
			if(strpos($address, '?') !== false)
				$dwoo->triggerError('Mailto: Hex encoding is not possible with extra attributes, use one of : <em>js, jscharcode or none</em>.', E_USER_WARNING);

			$out = '<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;';
			$len = strlen($address);
	        for ($i=0; $i<$len; $i++)
			{
				if(preg_match('#\w#', $address[$i]))
					$out .= '%'.bin2hex($address[$i]);
				else
					$out .= $address[$i];
			}
			$out .= '" '.$extra.'>';
			$len = strlen($text);
	        for ($i=0; $i<$len; $i++)
				$out .= '&#x'.bin2hex($text[$i]);
			return $out.'</a>';

		default:
			$dwoo->triggerError('Mailto: <em>encode</em> argument is invalid, it must be one of : <em>none (= no value), js, js_charcode or hex</em>', E_USER_WARNING);
	}
}

?>