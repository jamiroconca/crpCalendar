/**
 * crpLicense
 *
 * @copyright (c) 2009, Daniele Conca
 * @link http://code.zikula.org/crplicense Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpLicense
 */

function crpLicenseAdminViewInit()
{
	var real = $$('span.crp-status-real');
	
	real.each(
		function(node) 
		{ 
			node.addClassName('pn-hide');
		}
	)
	
	var fake = $$('span.crp-status-fake');
	
	fake.each(
		function(node) 
		{ 
			node.removeClassName('pn-hide');
		}
	)
}

function togglestatus(licenseid,status)
{
    var pars = "module=crpLicense&func=toggleStatus&id=" + licenseid
    			+"&status=" + status;
    var myAjax = new Ajax.Request(
        "ajax.php", 
        {
            method: 'get', 
            parameters: pars, 
            onComplete: togglestatus_response
        });
}

function togglestatus_response(req)
{
    if (req.status != 200 ) { 
        pnshowajaxerror(req.responseText);
        return;
    }
    
    var jsonArray = pndejsonize(req.responseText);

    $('licensestatus_fake_A_' + jsonArray.id).toggle();
    $('licensestatus_fake_P_' + jsonArray.id).toggle();
}

// key verification
function isNumeric(strString)//  check for valid numeric strings
{
	var strValidChars = "0123456789.-";
	var strChar;
	var blnResult = true;
	
	if (strString.length == 0) 
		return false;
	
	//  test strString consists of valid characters listed above
	for (k = 0; k < strString.length && blnResult == true; k++) {
		strChar = strString.charAt(k);
		if (strValidChars.indexOf(strChar) == -1) {
			blnResult = false;
		}
	}
	return blnResult;
}