<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once(dirname(__FILE__).'/../../classes/ScreenScrapePdfUrl.php');
$url = '';
if ( isset($_GET['url']) ) {
  $url = $_GET['url'];
}
?>
<pre>
<?php 
echo "start\n";
#$s = ScreenScrapePdfUrl::build('http://www.google.com');
#$s = ScreenScrapePdfUrl::build('http://ehis.ebscohost.com/ehost/pdfviewer/pdfviewer?vid=3&sid=76ac0bff-cb9f-44a7-98f3-68c9cb49b356%40sessionmgr115&hid=107');
#$s = ScreenScrapePdfUrl::build('http://www.sciencedirect.com/science/article/pii/S0002929712002583');
#$s = ScreenScrapePdfUrl::build('http://ovidsp.tx.ovid.com/sp-3.8.1a/ovidweb.cgi?WebLinkFrameset=1&S=PJAPFPLCGFDDAGLNNCOKMDJCGJKJAA00&returnUrl=ovidweb.cgi%3fMain%2bSearch%2bPage%3d1%26S%3dPJAPFPLCGFDDAGLNNCOKMDJCGJKJAA00&directlink=http%3a%2f%2fgraphics.tx.ovid.com%2fovftpdfs%2fFPDDNCJCMDLNGF00%2ffs046%2fovft%2flive%2fgv023%2f00000542%2f00000542-900000000-98460.pdf&filename=Fantastic+Delusions%2c+Futility+and+a+Family%27s+Love.&navigation_links=NavLinks.S.sh.18.1&link_from=S.sh.18|1&pdf_key=FPDDNCJCMDLNGF00&pdf_index=/fs046/ovft/live/gv023/00000542/00000542-900000000-98460&link_set=S.sh.18|1|sl_10|resultSet|S.sh.18.20|0');
#$s = ScreenScrapePdfUrl::build('http://ovidsp.tx.ovid.com/sp-3.8.1a/ovidweb.cgi?QS2=434f4e1a73d37e8cbc3152776b9fd747346b6efedd54280438975baf76e85d58cd3275413153de0a7cac6378913dae6c7d760d6d47be2ec71c955fea9c332268ff8f82141a23cbf332023f92d4812d2d590a29b665993245fa1618d49ce13c920ee0c954acee039f0a0cf3cb3fec448740b776ed729efad9ac73f82c2ffc8360f869103bc5f5615a3215c2e1653afd2ae01cf0bdc0a053ba0deea852130bb341897fc6c5deb034f9a86ae4e9e867474baf3be88d74cd7f684f657dc787f967b787162af011bf133ac5198bf3cbb1ceb1912ed3977fc00d666ca19c66797012cad79c99c812971ca2176531d216a09f996e4d5fb2bd495ed143a24f7b70baf6f637d01571eaad6201fe34fc91360128cd8ace0ece8a48a79a38dc1584b7b27d048967dde814a523236152911f66c781a8e8f5d65fffa484e7');
#$s = ScreenScrapePdfUrl::build('');
$s = ScreenScrapePdfUrl::build($url);
if ( $s !== FALSE && $s->getUrl() !== FALSE ) {
	echo '<a href="'.htmlspecialchars($s->getUrl()).'">PDF'."</a>\n";
} else {
	echo 'ScreenScrapePdfUrl is FALSE';
}
echo "\nstop\n";
#echo var_dump($s);
?>
</pre>
