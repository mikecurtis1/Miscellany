<?php 
$url = 'http://www.example.com:8080/X?op=find-doc&doc_num='.$_GET['doc_num'].'&base=BIB01';
$xml = file_get_contents($url);
$resource = new SimpleXMLElement($xml);
$marc_arr = array();
if ( isset($resource->record->metadata->oai_marc->fixfield) ) {
	foreach ( $resource->record->metadata->oai_marc->fixfield as $n => $obj ) {
		$arr = (array) $obj;
		$fixfield_id = $arr['@attributes']['id'];
		$fixfield_text = (string) $obj;
		$marc_arr[$fixfield_id][] = array('fixfield'=>$fixfield_text);
	}
}
if ( isset($resource->record->metadata->oai_marc->varfield) ) {
	foreach ( $resource->record->metadata->oai_marc->varfield as $n => $obj ) {
		$arr = (array) $obj;
		$id = '';
		$i1 = '';
		$i2 = '';
		foreach ( $arr['@attributes'] as $i => $attr ) {
			if ( $i === 'id' ) {
				$id = $attr;
			}
			if ( $i === 'i1' ) {
				$i1 = $attr;
			}
			if ( $i === 'i2' ) {
				$i2 = $attr;
			}
		}
		$count = count($obj->subfield);
		if ( $count == 1 ) {
			$data = (array) $obj->subfield;
			$subfield_text = $data[0];
			$subfield_label = $data['@attributes']['label'];
			$subfield_identifiers = array('i1'=>$i1,'i2'=>$i2);
			$subfield_data = array($subfield_label => array($subfield_text));
			$marc_arr[$id][] = array('identifiers'=>$subfield_identifiers,'subfields'=>$subfield_data);
		} elseif ( $count >= 1 ) {
			$subfields_str = '';
			$subfields_arr = array();
			foreach ( $obj as $i => $v ) {
				$subfield_text = (string) $v;
				$x = (array) $v;
				$subfield_label = $x['@attributes']['label'];
				$subfields_str .= $subfield_label.'|'.$subfield_text.' ';
				$subfields_arr[$subfield_label][] = $subfield_text;
			}
			$subfield_identifiers = array('i1'=>$i1,'i2'=>$i2);
			$marc_arr[$id][] = array('identifiers'=>$subfield_identifiers,'subfields'=>$subfields_arr);
		}
	}
}
?>
<pre>
<?php 
foreach ( $marc_arr as $id => $field ) {
	foreach ( $field as $n => $data ) {
		if ( isset($data['fixfield']) ) {
			echo $id.' '.trim($data['fixfield'])."\n";
		}
		if ( isset($data['subfields']) ) {
			$subfields_str = '';
			foreach ( $data['subfields'] as $label => $text ) {
				$subfields_str .= $label.'|'.$text[0].' ';
			}
			$i1 = str_replace(' ','_',$data['identifiers']['i1']);
			$i2 = str_replace(' ','_',$data['identifiers']['i2']);
			echo $id.$i1.$i2.' '.trim($subfields_str)."\n";
		}
	}
}
?>
<hr />
<?php print_r($marc_arr); ?>
<hr />
<?php echo htmlspecialchars($xml); ?>
</pre>
