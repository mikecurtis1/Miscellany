<?php header('Content-Type: text/html; charset=utf-8');?>
<pre>
<?php
require_once 'wskey.php';

$kw_phrase = '';
$li_phrase = 'srw.li = "YJE" OR srw.li = "BEQ" OR srw.li = "BBB" OR srw.li = "BSJ" OR srw.li = "VHB" OR srw.li = "YJQ" OR srw.li = "VTP" OR srw.li = "VJF" OR srw.li = "R6A" OR srw.li = "NYB" OR srw.li = "VOQ" OR srw.li = "BPNYA" OR srw.li = "NoI" OR srw.li = "VKC" OR srw.li = "NYC" OR srw.li = "VOV" OR srw.li = "ZCQ" OR srw.li = "VXU" OR srw.li = "VYK" OR srw.li = "YJJ" OR srw.li = "VVH" OR srw.li = "VUF" OR srw.li = "VOL" OR srw.li = "VOO" OR srw.li = "YJN" OR srw.li = "YJL" OR srw.li = "YJO" OR srw.li = "YJP" OR srw.li = "MWAFL" OR srw.li = "YJA" OR srw.li = "GF#" OR srw.li = "YKH" OR srw.li = "YJD" OR srw.li = "NYT" OR srw.li = "YKJ" OR srw.li = "CC3" OR srw.li = "JDC" OR srw.li = "YKL" OR srw.li = "NYW" OR srw.li = "NYL" OR srw.li = "YJT" OR srw.li = "YJV" OR srw.li = "YJU" OR srw.li = "GM@" OR srw.li = "YKQ" OR srw.li = "NYM" OR srw.li = "NYP" OR srw.li = "YKU" OR srw.li = "NYN" OR srw.li = "YKX" OR srw.li = "VVN" OR srw.li = "ZNM" OR srw.li = "VOX" OR srw.li = "YLD" OR srw.li = "YJZ" OR srw.li = "RTP" OR srw.li = "VZR" OR srw.li = "YKA" OR srw.li = "VUV" OR srw.li = "VYS" OR srw.li = "YKD" OR srw.li = "YBM" OR srw.li = "XFM" OR srw.li = "YKE" OR srw.li = "URP" OR srw.li = "VUG" OR srw.li = "AEF" OR srw.li = "SBH" OR srw.li = "SBL" OR srw.li = "BUF" OR srw.li = "YKF" OR srw.li = "YKN" OR srw.li = "YKK" OR srw.li = "VZX" OR srw.li = "YJI"';
if ( isset($_GET['kw']) ) {
    $kw_phrase = 'srw.kw all "' . $_GET['kw'] . '"';
}
$query = '(' . $kw_phrase . ') AND (' . $li_phrase . ')';

$srw_url = 'http://www.worldcat.org/webservices/catalog/search/sru?operation=searchRetrieve&version=1.1&query=' . urlencode($query) . '&startRecord1&maximumRecords=10&recordPacking=xml&recordSchema=marcxml&sortKeys=Relevance&servicelevel=full&frbrGrouping=on&wskey=' . urlencode($wskey);

#echo $srw_url;
#echo htmlspecialchars(file_get_contents($srw_url));

$xml = file_get_contents($srw_url);

$x = new SimpleXMLElement($xml, null, null, 'http://www.loc.gov/zing/srw/', false);
$x->registerXPathNamespace('srw', 'http://www.loc.gov/zing/srw/');
$x->registerXPathNamespace('slim', 'http://www.loc.gov/MARC21/slim');

$query_api = $x->echoedSearchRetrieveRequest->query;
echo $query_api . "\n";
echo "<hr />\n";

if ( $arr = $x->xpath('//srw:searchRetrieveResponse/srw:records/srw:record/srw:recordData/slim:record') ) {
    foreach ( $arr as $obj ) {
        $obj->registerXPathNamespace('slim', 'http://www.loc.gov/MARC21/slim');
        //
        $marc100 = $obj->xpath("slim:datafield[@tag='100']/slim:subfield[@code='a']");
        $author = xpathArrToText($marc100);
        //
        $title = $obj->xpath("slim:datafield[@tag='245']");
        $ti = xpathArrToText($title);
        //
        $marc001 = $obj->xpath("slim:controlfield[@tag='001']");
        $id = xpathArrToText($marc001);
        //
        $marc020 = $obj->xpath("slim:datafield[@tag='020']");
        $isbn = xpathArrToText($marc020);
        //
        $leader = $obj->xpath("slim:leader");
        $leader_str = xpathArrToText($leader);
        //
        $marc300 = $obj->xpath("slim:datafield[@tag='300']/slim:subfield[@code='a']");
        $pagination = xpathArrToText($marc300);
        //
        $marc260 = $obj->xpath("slim:datafield[@tag='260']/slim:subfield[@code='c']");
        $date = xpathArrToText($marc260);
        //
        $media_type = getMediaType($leader_str);
        //
        $marc520 = $obj->xpath("slim:datafield[@tag='520']/slim:subfield[@code='a']");
        $summary = xpathArrToText($marc520);
        //
        $marc505 = $obj->xpath("slim:datafield[@tag='505']/slim:subfield[@code='a']");
        $toc = xpathArrToText($marc505);
        //
        echo var_dump($id);
        echo var_dump($isbn);
        echo var_dump($leader_str);
        echo var_dump(removeTrailingPunctuation($author));
        echo var_dump($ti);
        echo var_dump($summary);
        echo var_dump($toc);
        echo var_dump(removeTrailingPunctuation($pagination));
        echo var_dump(removeTrailingPunctuation($date));
        echo var_dump($media_type);
        echo "<hr />\n";
    }
}

function xpathArrToText($arr=array()) {
    $string = '';
    if ( isset($arr[0]) ) {
        $string = strip_tags($arr[0]->asXML());
        $string = str_replace("\r",'',$string);
        $string = str_replace("\n",'',$string);
        $string = preg_replace('/\s{2,}/', ' ', trim($string));
    }
    
    return $string;
}

function getMediaType($leader=''){
    $substr = substr($leader,6,2);
    if ( $substr === 'am' ) { // a = Language material, m = Monograph/Item
        return 'book';
    } elseif ( $substr === 'cm' ) { // c = Notated music, m = Monograph/Item
        return 'score';
    } elseif ( $substr === 'em' ) { // e = Cartographic material, m = Monograph/Item
        return 'map';
    } elseif ( $substr === 'im' ) { // i = Nonmusical sound recording, m = Monograph/Item
        return 'audio';
    } elseif ( $substr === 'jm' ) { // j = Musical sound recording, m = Monograph/Item
        return 'audio';
    } elseif ( $substr === 'gm' ) { // g = Projected medium, m = Monograph/Item
        return 'video';
    } else {
        return '';
    }
}

function removeTrailingPunctuation($str=''){
    $arr = array('.',',',':',';','/');
    $str = trim($str);
    if ( in_array(substr($str,-1), $arr) === TRUE ) {
        return trim(substr($str,0,-1));
    }
    
    return trim($str);
}

echo var_dump($x);
?>
</pre>
