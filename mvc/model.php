<?php 

class Rooms{

	private $url = "http://ups.sunyconnect.suny.edu:4360/X?op=circ_status&sys_no=000087884&library=ups01";

	public function __construct(){
		$this->z30_description = NULL;
		$this->loan_status = NULL;
		$this->due_hour = NULL;
		$this->due_date = NULL;
		$this->availability = NULL;
		$this->availability_statement = NULL;
		$this->style = NULL;
		$this->items = $this->getItemElements();
	}

	private function getItemElements(){
		// TODO: some cache function should be used to reduce Aleph requests
		$xml = file_get_contents($this->url);
		$xmlObject = new SimpleXMLElement($xml);
		
		return $xmlObject->xpath("item-data");
	}
	
	/*private function parseItem($xmlObject){
		$z30_description = (array) $xmlObject->xpath("z30-description");
		
		return $z30_description;
	}*/

}

?>
