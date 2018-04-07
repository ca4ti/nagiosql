///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project  : NagiosQL
// Component: Installer Javascript Functions
// Website  : http://www.nagiosql.org
// Date     : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.0.4
// Revision : $LastChangedRevision: 827 $
//
///////////////////////////////////////////////////////////////////////////////

// Hide/Show +/- content elements
function Klappen(Id) {
	var KlappText = document.getElementById('SwTxt'+Id);
	var KlappBild = document.getElementById('SwPic'+Id);
	var KlappMinus="images/minus.png", KlappPlus="images/plus.png";
	if (KlappText.style.display == 'none') {
		KlappText.style.display = 'block';
		KlappBild.src = KlappMinus;
	} else {
		KlappText.style.display = 'none';
		KlappBild.src = KlappPlus;
	}
}
