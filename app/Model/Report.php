<?php
App::uses('AppModel', 'Model');
/**
 * Document Report
 *
 * @property DocumentCategory $DocumentCategory
*/
class Report extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
			'DocumentCategory' => array(
					'className' => 'DocumentCategory',
					'foreignKey' => 'document_category_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);

	public function generateReport($id, $clientId) {
		$this->id = $id;
		$function = $this->field("function");
		$this->$function($clientId);
	}

	private function setupPDFDocument($pdf, $title, $clientId) {
		App::import('Model', 'Client');
		$Client = new Client();
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle('Index File');

		// set default header data
		$pdf->SetHeaderData("@".$Client->getClientCompanyLogo($clientId), 30, $title, PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', '', 14, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		return $pdf;
	}

	public function generateBarcodeDoc($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Client');
		App::import('Model', 'Device');
		$client = new Client();
		$deviceModel = new Device();
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Client Barcodes', $clientId);
		// set style for barcode
		$style = array(
				'border' => 2,
				'vpadding' => 'auto',
				'hpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				'bgcolor' => false, //array(255,255,255)
				'module_width' => 1, // width of a single module in points
				'module_height' => 1 // height of a single module in points
		);
		// Set some content to print
		$clientData = $client->find('first', array('conditions'=>array('Client.id'=>$clientId)));
		$devices = $clientData['Device'];
		$barcodex = 20;
		$barcodey = 40;
		foreach ($devices as $device) {
			$deviceData = $deviceModel->find('first', array('conditions'=>array('Device.id'=>$device['id'])));
			$device = $deviceData['Device'];
			$location = $deviceData['Location']['name'];
			$type = $deviceData['DeviceType']['name'];
			if ($device['active'] == 1) {
				$pdf->write2DBarcode($device['id'], 'QRCODE,M', $barcodex, $barcodey, 50, 50, $style, 'N');
				$pdf->SetXY($barcodex, $barcodey - 12);
				$pdf->Cell(50, 0, $device['label'] . ", " . $type, 0, 0, 'C', false, '', 1);
				$pdf->SetXY($barcodex, $barcodey - 7);
				$pdf->Cell(50, 0, $location, 0, 0, 'C', false, '', 1);
				if ($barcodex == 110) {
					$barcodex = 20;
					$barcodey = $barcodey + 80;
				}
				else $barcodex = $barcodex + 90;
				if ($barcodey == 280) {
					$pdf->AddPage();
					$barcodey = 40;
				}
			}
		}
		$pdf->Output('barcodes.pdf', 'I');
	}
	
	public function generateBarcodeDocFromIds($clientId, $ids) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Device');
		$deviceModel = new Device();
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Client Barcodes', $clientId);
		// set style for barcode
		$style = array(
				'border' => 2,
				'vpadding' => 'auto',
				'hpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				'bgcolor' => false, //array(255,255,255)
				'module_width' => 1, // width of a single module in points
				'module_height' => 1 // height of a single module in points
		);
		// Set some content to print

		$barcodex = 20;
		$barcodey = 40;
		foreach ($ids as $device) {
			$deviceData = $deviceModel->find('first', array('conditions'=>array('Device.id'=>$device)));
			$device = $deviceData['Device'];
			$location = $deviceData['Location']['name'];
			$type = $deviceData['DeviceType']['name'];
			if ($device['active'] == 1) {
				$pdf->write2DBarcode($device['id'], 'QRCODE,M', $barcodex, $barcodey, 50, 50, $style, 'N');
				$pdf->SetXY($barcodex, $barcodey - 12);
				$pdf->Cell(50, 0, $device['label'] . ", " . $type, 0, 0, 'C', false, '', 1);
				$pdf->SetXY($barcodex, $barcodey - 7);
				$pdf->Cell(50, 0, $location, 0, 0, 'C', false, '', 1);
				if ($barcodex == 110) {
					$barcodex = 20;
					$barcodey = $barcodey + 80;
				}
				else $barcodex = $barcodex + 90;
				if ($barcodey == 280) {
					$pdf->AddPage();
					$barcodey = 40;
				}
			}
		}
		$pdf->Output('barcodes.pdf', 'I');
	}

	public function generate70x40BarcodeDocFromIds($clientId, $ids) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Device');
		$deviceModel = new Device();
		// create new PDF document
		$pagelayout = array(70, 40);
		$pdf = new TCPDF('landscape', "mm", $pagelayout, true, 'UTF-8', false);
		$pdf->setPageOrientation('landscape', false, 0);
		//$pdf = $this->setupPDFDocument($pdf, 'Client Barcodes', $clientId);
		// set style for barcode
		$style = array(
				'border' => 2,
				'vpadding' => 1,
				'hpadding' => 1,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false, //array(255,255,255),
				'position' => 'R' 
		);
		// Set some content to print
		foreach ($ids as $device) {
			$pdf->AddPage();
			$deviceData = $deviceModel->find('first', array('conditions'=>array('Device.id'=>$device)));
			$device = $deviceData['Device'];
			$location = $deviceData['Location']['name'];
			$type = $deviceData['DeviceType']['name'];			
			if ($device['active'] == 1) {		
				$pdf->SetFontSize(10);
				$pdf->SetXY(2,5);
				$pdf->Cell(38, 0, "ServiceControl Device", 0, 0, 'C', false, '', 1);
				$pdf->SetFontSize(8);
				$pdf->SetXY(2,10);
				$pdf->Cell(38, 0, $device['label'] . ", " . $type, 0, 6, 'C', false, '', 1);
				$pdf->SetFontSize(6);
				$pdf->SetXY(2,15);
				$pdf->Cell(38, 0, $location, 0, 6, 'C', false, '', 1);
				$pdf->SetFontSize(10);
				//$pdf->write1DBarcode($device['id'], 'C39E+', 8, 20, 27, 15, '', array('border'=>2));
				$pdf->write2DBarcode($device['id'], 'QRCODE,H', 40, 5, 35, 35, $style, 'M', false);
			}
		}
		$pdf->Output('barcodes.pdf', 'I');
	}
	

	public function sortShortDates($a, $b) {
		$datea = strtotime($a);
		$dateb = strtotime($b);
		if ($datea == $dateb) return 0;
		return ($datea > $dateb) ? 1 : -1;
	}

	public function generateActivityLogs($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		// create new PDF document
		$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Activity Logs', $clientId);
		/// Get some data
		App::import('Model', 'Device');
		$devices = new Device();
		$devices->unbindModel(array('hasMany'=>array('DeviceService')));
		$deviceData = $devices->find('all', array('conditions'=>array('Device.client_id'=>$clientId)));
		$trapTypes = array("Cockroach Monitor", "Bait Box (internal)","Bait Box (external)", "Snap Trap", "Ant check area (internal)", "Ant check area (external)");
		$data = array();
		for ($i=0; $i!=sizeof($deviceData); $i++) {
			foreach ($deviceData[$i]['DevicesVisit'] as $check) {
				$results = json_decode($check['results'], true);
				if (isset($results['activity'])) $data["Y".date("Y", strtotime($check['time']))][$deviceData[$i]['DeviceType']['name']][$i][date("d M", strtotime($check['time']))] = strtoupper(substr($results['activity'],0,1));
				else $data["Y".date("Y", strtotime($check['time']))][$deviceData[$i]['DeviceType']['name']][$i][date("d M", strtotime($check['time']))] = "N";
			}
		}
		// Set some content to print
		krsort($data); //sort by year
		$yearKeys = array_keys($data);
		for ($i = 0; $i != sizeof($data); $i++) {
			$yearData = $data[$yearKeys[$i]];
			$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',20);
			$pdf->writeHtml("Activity Reports for ".substr($yearKeys[$i], 1));
			//set legend
			$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',9);
			$pdf->SetColor("text", 255, 0, 0);
			$pdf->SetXY(200, 25);
			$pdf->Cell(75,4, "Legend / Keys", 1, 0, 'C');
			$pdf->SetColor("text", 0);
			$pdf->Ln();
			$pdf->SetXY(200, 29);
			$pdf->Cell(15,0, "Y",1, 0, 'C');
			$pdf->Cell(60,0, "Activity found",1);
			$pdf->Ln();
			$pdf->SetXY(200, 33);
			$pdf->Cell(15,0, "N",1, 0, 'C');
			$pdf->Cell(60,0, "No activity found",1);
			$pdf->Ln();
			$pdf->SetXY(200, 37);
			$pdf->Cell(15,0, "(blank)",1, 0, 'C');
			$pdf->Cell(60,0, "No data collected or device not installed",1);
			$pdf->SetXY(15, 45);
			foreach($yearData as $trapName => $trapType) {
				if (in_array($trapName, $trapTypes)) {
					$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',14);
					$pdf->writeHtml("Activity for ". $trapName. " stations");
					$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',10);
					$allDates = array();
					foreach ($trapType as $device) $allDates = array_merge($allDates, $device);
					$pdf->Cell(15, 0, "BOX NO.", 1, 0, 'C', false, '', 1);
					$dates = array_keys($allDates);
					usort($dates, array($this, 'sortShortDates'));
					for ($j = 0; $j < 17; $j++) {
						if ($j < sizeof($dates)) $pdf->Cell(15, 0, $dates[$j], 1, 0, 'C', false, '', 1);
						else $pdf->Cell(15, 0, "", 1, 0, 'C', false, '', 1);
					}
					$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',10);
					$pdf->Ln();
					$count = 1;
					foreach ($trapType as $device) {
						$pdf->Cell(15, 0, $count.".", 1, 0, 'C', false, '', 1);
						$count++;
						for ($j = 0; $j < 17; $j++) {
							if ($j < sizeof($dates) && isset($device[$dates[$j]])) $pdf->Cell(15, 0, $device[$dates[$j]], 1, 0, 'C', false, '', 1);
							else $pdf->Cell(15, 0, "", 1, 0, 'C', false, '', 1);
						}
						$pdf->Ln();
					}
					$pdf->Ln();
					$pdf->Ln();
				}
			}
			if ($i != sizeof($data) -1) $pdf->AddPage();
		}
		// Set some content to print
		$pdf->Output('report.pdf', 'I');
	}

	public function generateEFKTrendReport($clientId) {
		App::import('Vendor', 'SVGGraph/SVGGraph');
		App::import('Vendor', 'tcpdf_min/tcpdf');
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'ELECTRONIC FLY KILLER TREND REPORT', $clientId);
		/// Get some data
		App::import('Model', 'Visit');
		$visits = new Visit();
		$visits->unbindModel(array('hasMany'=>array('ActionLog', 'ServiceReport', 'Sighting', 'DeviceService'), 'hasAndBelongsToMany' => array('Chemical')));
		$visitData = $visits->find('all', array('conditions'=>array('Client.id'=>$clientId), 'recursive'=>2, 'order'=>array("Visit.modified"=>"desc")));
		// Set some content to print
		$types = array("houseFly", "greenBottle", "blueBottle", "fruitFly", "bees", "nightFlyers", "other");
		$legend = array("House Fly", "Green Bottle", "Blue Bottle", "Fruit Fly", "Bees", "Night Flyers", "Other");
		foreach ($visitData as $visit) {
			foreach ($visit['Device'] as $device) {
				if ($device['DevicesVisit']['action'] == "check" && $device['DeviceType']['name'] == "EFK (Fly Trap)") {
					$date = strtotime($device['DevicesVisit']['time']);
					$year = date('Y', $date);
					$month = date('n', $date) - 1;
					$results = json_decode($device['DevicesVisit']['results'], true);
					if (isset($results['typeSpecific'])) {
						foreach ($types as $type) {
							if (isset($results['typeSpecific'][$type])) {
								if (isset($data["Y$year"][$month][$type])) $data["Y$year"][$month][$type] = $data["Y$year"][$month][$type] + $results['typeSpecific'][$type];
								else $data["Y$year"][$month][$type] = $results['typeSpecific'][$type];
							}
						}
					}
				}
			}
		}
		krsort($data); //sort by year
		$yearKeys = array_keys($data);
		for ($i = 0; $i != sizeof($data); $i++) {
			$values = array();
			$yearData = $data[$yearKeys[$i]];
			$count = 0;
			foreach ($types as $type) {
				$typeArray = $this->getYearArray();
				$monthKeys = array_keys($typeArray);
				for ($j=0;$j!=12;$j++) {
					if (isset($yearData[$j][$type])) $typeArray[$monthKeys[$j]] = $yearData[$j][$type];
					else $typeArray[$monthKeys[$j]] = 0;
				}
				array_push($values, $typeArray);
				$count++;
			}
			$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',16);
			$pdf->writeHtml("EFK Trends for " . substr($yearKeys[$i], 1));
			$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',10);
			$pdf->Ln();
			$pdf->Ln();
			$settings = array("legend_entries"=>$legend, "label_x"=>"Month in ". substr($yearKeys[$i], 1), "label_y"=>"Total Count", "label_font_size"=>15, 'axis_font_size'=>12);
			$graph = new SVGGraph(700, 400, $settings);
			$graph->Values($values);
			$imgStr = $graph->Fetch('MultiLineGraph', false);
			$pdf->ImageSVG("@".$imgStr, '','',0, 0, '', 'M', 'C', 0, true);
			if ($i != sizeof($data) -1) $pdf->AddPage();
		}
		$pdf->Output('report.pdf', 'I');
	}


	public function generateStandardTrendReports($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Vendor', 'SVGGraph/SVGGraph');
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Trend Reports', $clientId);
		/// Get some data
		App::import('Model', 'Visit');
		$visits = new Visit();
		$visits->unbindModel(array('hasMany'=>array('ActionLog', 'ServiceReport', 'Sighting', 'DeviceService'), 'hasAndBelongsToMany' => array('Chemical')));
		$visitData = $visits->find('all', array('conditions'=>array('Client.id'=>$clientId), 'recursive'=>2, 'order'=>array("Visit.modified"=>"desc")));
		$trapTypes = array(array("Cockroach Monitor"), array("Bait Box (internal)"), array("Bait Box (external)", "Snap Trap"), array("Ant check area (internal)", "Ant check area (external)"));
		$trapLegends = array("Cockroaches", "Rodents (internal)", "Rodents (external)", "Black Ants");
		$data = array();
		foreach ($visitData as $visit) {
			foreach ($visit['Device'] as $device) {
				if ($device['DevicesVisit']['action'] == "check" && $device['DeviceType']['name'] != "EFK (Fly Trap)") {
					$date = strtotime($device['DevicesVisit']['time']);
					$year = date('Y', $date);
					$month = date('n', $date) - 1;
					$results = json_decode($device['DevicesVisit']['results'], true);
					if (isset($results['activity'])) {
						for ($i = 0; $i != sizeof($trapTypes); $i++) {
							foreach ($trapTypes[$i] as $trapType) {
								if ($trapType == $device['DevicesVisit']['device_type'] && $results['activity'] == "yes") {
									if (isset($data["Y$year"][$month][$trapLegends[$i]])) $data["Y$year"][$month][$trapLegends[$i]] = $data["Y$year"][$month][$trapLegends[$i]] + 1;
									else $data["Y$year"][$month][$trapLegends[$i]] = 1;
								}
							}
						}
					}
				}
			}
		}
		// Set some content to print
		krsort($data); //sort by year
		$yearKeys = array_keys($data);
		for ($i = 0; $i != sizeof($data); $i++) {
			$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',26);
			$pdf->writeHtml("Trend Graphs for " . substr($yearKeys[$i], 1));
			$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',10);
			$pdf->Ln();
			$pdf->Ln();
			$yearData = $data[$yearKeys[$i]];
			foreach ($trapLegends as $trapTypeGraph) {
				$yearValues = $this->getYearArray();
				$monthKeys = array_keys($yearValues);
				for ($j=0;$j!=12;$j++) {
					if (isset($yearData[$j][$trapTypeGraph])) $yearValues[$monthKeys[$j]] = $yearData[$j][$trapTypeGraph];
					else $yearValues[$monthKeys[$j]] = 0;
				}
				for ($j=0;$j!=12;$j++) {
					if ($yearValues[$monthKeys[$j]] > 0) $isData = true;
					else $isData = false;
				}
				if (!$isData) $yearValues[$monthKeys[0]] = 0.1;
				$settings = array("graph_title"=>"Trend Report for ".$trapTypeGraph, "graph_title_position"=>"bottom", "graph_title_font_weight" =>"bold",
						"label_x"=>"Month in ". substr($yearKeys[$i], 1), "label_y"=>"Total Activity Count", "label_font_size"=>15, 'axis_font_size'=>12);
				$graph = new SVGGraph(390, 300, $settings);
				$graph->Values($yearValues);
				$imgStr = $graph->Fetch('LineGraph', false);
				$pdf->ImageSVG("@".$imgStr, '', 50, 170, 150, '', 'M', 'C', 0, false);
				$pdf->AddPage();
			}
			$pdf->deletePage($pdf->getNumPages());
			if ($i != sizeof($data) -1) $pdf->AddPage();
		}
		$pdf->Output('report.pdf', 'I');
	}

	function compareChemicalVisits($a, $b) {
		if ($a['time'] == $b['time']) return 0;
		return ($a['time'] < $b['time']) ? 1 : -1;
	}

	public function generateChemicalUsageLog($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		// create new PDF document
		$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Chemical usage log', $clientId);
		//get some data
		App::import('Model', 'Visit');
		$visits = new Visit();
		$visits->unbindModel(array('hasMany'=>array('ActionLog', 'Sighting', 'DeviceService'), 'hasAndBelongsToMany' => array('Device')));
		$visitData = $visits->find('all', array('conditions'=>array('Client.id'=>$clientId), 'order'=>array("Visit.modified"=>"desc")));
		$data = array();
		foreach ($visitData as $visit)
		foreach ($visit['Chemical'] as $chemical)
		if (isset($chemical['ChemicalsVisit']))	{
			$chemical['ChemicalsVisit']['chemicalName'] = $chemical['name'];
			$chemical['ChemicalsVisit']['technicianName'] = $visit['Technician']['name'];
			$chemical['ChemicalsVisit']['chemicalCode'] = $chemical['code'];
			array_push($data, $chemical['ChemicalsVisit']);
		}
		usort($data, array($this, "compareChemicalVisits"));
		// Set some content to print
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',16);
		$pdf->writeHtml("CHEMICAL USAGE LOG");
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',10);
		$pdf->Ln();
		$pdf->Cell(30, 0, "DATE & TIME", 1, 0, 'L', false, '', 1);
		$pdf->Cell(30, 0, "PC OPERATOR", 1, 0, 'L', false, '', 1);
		$pdf->Cell(35, 0, "AREA OF APPLICATION", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "PEST ACTIVITY", 1, 0, 'L', false, '', 1);
		$pdf->Cell(30, 0, "CHEMICAL USED", 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "L NUMBER", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "BATCH NUMBER", 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "DILUTION", 1, 0, 'L', false, '', 1);
		$pdf->Cell(30, 0, "METHOD", 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "DOSAGE", 1, 0, 'L', false, '', 1);
		$pdf->Ln();
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',10);
		foreach ($data as $row) {
			$pdf->Cell(30, 0, $row['time'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(30, 0, $row['technicianName'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(35, 0, $row['areaUsed'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(25, 0, strtoupper($row['pestActivity']), 1, 0, 'C', false, '', 1);
			$pdf->Cell(30, 0, $row['chemicalName'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(20, 0, $row['chemicalCode'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(25, 0, $row['batch'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(20, 0, $row['dilution'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(30, 0, $row['applicationType'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(20, 0, $row['dosage'], 1, 0, 'L', false, '', 1);
			$pdf->Ln();
		}
		$pdf->Output('report.pdf', 'I');
	}

	function compareServiceDates($a, $b) {
		if ($a['serviceDate'] == $b['serviceDate']) return 0;
		return ($a['serviceDate'] < $b['serviceDate']) ? 1 : -1;
	}

	public function generateEFKLightReplacementSchedule($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Visit');
		App::import('Model', 'Media');
		App::import('Model', 'Device');
		$devices = new Device();
		$visits = new Visit();
		$visits->unbindModel(array('hasMany'=>array('ActionLog', 'Sighting'), 'hasAndBelongsToMany' => array('Chemical', 'Device')));
		$visitData = $visits->find('all', array('conditions'=>array('Client.id'=>$clientId)));
		$data = array();
	
		foreach ($visitData as $visit)
		foreach ($visit['DeviceService'] as $service)
		if (isset($service['serviceType']) && $service['serviceType'] == "EFK Light Replacement") {
			$device = $devices->find('first', array('conditions'=>array('Device.id'=>$service['device_id'])));
			if (isset($visit['ServiceReport'][0]['technicianSignature'])) $service['technicianSignature'] = $visit['ServiceReport'][0]['technicianSignature'];
			if (isset($visit['ServiceReport'][0]['clientSignature'])) $service['clientSignature'] = $visit['ServiceReport'][0]['clientSignature'];
			$service['deviceArea'] = $device['Device']['location'];
			$service['deviceInstalled'] = $device['Device']['installedDate'];
			$service['daysBetween'] = $device['Device']['daysBetweenSpecialService'];
			array_push($data, $service);
		}
		usort($data, array($this, "compareServiceDates"));
		// create new PDF document
		$pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Globe replacement schedule', $clientId);
		// Set some content to print
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',16);
		$pdf->writeHtml("ELECTRONIC FLY KILLER GLOBE REPLACEMENT SCHEDULE");
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',10);
		$pdf->Ln();
		$pdf->Cell(35, 8, "DATE INSTALLED", 1, 0, 'L', false, '', 1);
		$pdf->Cell(50, 8, "AREA INSTALLED", 1, 0, 'L', false, '', 1);
		$pdf->Cell(35, 8, "NEXT REPLACEMENT DATE", 1, 0, 'L', false, '', 1);
		$pdf->Cell(35, 8, "DATE REPLACED", 1, 0, 'L', false, '', 1);
		$pdf->Cell(55, 8, "PCO SIGNATURE", 1, 0, 'L', false, '', 1);
		$pdf->Cell(55, 8, "CLIENT SIGNATURE", 1, 0, 'L', false, '', 1);
		$pdf->Ln(8);
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',10);
		foreach ($data as $row) {
			$pdf->Cell(35, 8, $row['deviceInstalled'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(50, 8, $row['deviceArea'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(35, 8, date("Y-m-d", strtotime($row['serviceDate'] . " + " . $service['daysBetween']. " days")), 1, 0, 'L', false, '', 1);
			$pdf->Cell(35, 8, $row['serviceDate'], 1, 0, 'L', false, '', 1);
			$pdf->SetFontSize(0);
			$image = new Media();
			if (isset($row['technicianSignature'])) {
				$image->id = $row['technicianSignature'];
				$pdf->Cell(55, 8, $pdf->Image('@'.$image->field('data'), 185, '', 0, 9, '', '', 'C', false, 300, '', false, false, 0, false, false, false, false, array()), 1, 0, 'L', false, '', 1);
			}
			else $pdf->Cell(55, 8, "", 1, 0, 'L', false, '', 1);
			if (isset($row['clientSignature'])) {
				$image->create();
				$image->id = $row['clientSignature'];
				$pdf->Cell(55, 8, $pdf->Image('@'.$image->field('data'), 240, '', 0, 9, '', '', 'C', false, 300, '', false, false, 0, false, false, false, false, array()), 1, 0, 'L', false, '', 1);
			}
			else $pdf->Cell(55, 8, "", 1, 0, 'L', false, '', 1);
			$pdf->SetFontSize(10);
			$pdf->Ln(8);
		}
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',10);
		$pdf->Output('report.pdf', 'I');
	}

	public function generateActionLog($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Visit');
		App::import('Model', 'Media');
		App::import('Model', 'Sighting');
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Corrective action reports', $clientId);
		$visits = new Visit();
		$sighting = new Sighting();
		$visits->unbindModel(array('hasMany'=>array('Sighting', 'DeviceService'), 'hasAndBelongsToMany' => array('Chemical')));
		$visitData = $visits->find('all', array('conditions'=>array('Client.id'=>$clientId), 'order'=>array("Visit.modified"=>"asc")));
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Corrective action report', $clientId);
		// Set some content to print
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',16);
		$pdf->writeHtml("CORRECTIVE ACTION REPORT");
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',10);
		$pdf->Ln();
		$this->newActionLogTable($pdf, true);
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',10);
		foreach($visitData as $visit) {
			if (sizeof($visit['ActionLog']) == 1) {
				if ($visit['ActionLog'][0]['nextVisitId'] > 0) {
					$additionalVisit = new Visit();
					$nextVisitData = $additionalVisit->find('first', array('conditions'=>array('Visit.id'=>$visit['ActionLog'][0]['nextVisitId'])));
					$nextVisit = date("Y-m-d", strtotime($nextVisitData['Visit']['timeDue']));
				}
				else $nextVisit = "not required";
				$activityFound = "No";
				foreach ($visit['Device'] as $device){
					if (isset($device['DevicesVisit']['results'])) {
						$results = json_decode($device['DevicesVisit']['results'], true);
						if ($results['activity'] == "yes") $activityFound = "Yes"; 
					}
				}
				$sighting->id = $visit['ActionLog'][0]['sighting_id'];
				$pdf->Cell(20, 5, date("Y-m-d", strtotime($visit['Visit']['modified'])), 1, 0, 'L', false, '', 1);
				$pdf->Cell(25, 5, $visit['ActionLog'][0]['followUpNumber'], 1, 0, 'C', false, '', 1);
				$pdf->Cell(25, 5, $activityFound, 1, 0, 'C', false, '', 1);
				$pdf->Cell(25, 5, $sighting->field('areaOfSighting'), 1, 0, 'L', false, '', 1);
				$pdf->Cell(30, 5, $visit['ActionLog'][0]['correctiveAction'], 1, 0, 'L', false, '', 1);
				$pdf->Cell(20, 5, $nextVisit, 1, 0, 'C', false, '', 1);
				if (sizeof($visit['ServiceReport']) == 1)  {
					$pdf->SetFontSize(0);
					$image = new Media();
					$image->id = $visit['ServiceReport'][0]['technicianSignature'];
					$pdf->Cell(35, 5, $pdf->Image('@'.$image->field('data'), 170, '', 0, 6, '', '', 'C', false, 300, '', false, false, 0, false, false, false, false, array()), 1, 0, 'L', false, '', 1);
				}
				else $pdf->Cell(35, 5, "", 1, 0, 'L', false, '', 1);
				$pdf->SetFontSize(10);
				$pdf->Ln(5);
			}
		}
		$pdf->Output('report.pdf', 'I');
	}
	
	public function newActionLogTable($pdf, $sighting = true) {
		$pdf->Cell(20, 0, "DATE", 1, 0, 'L', false, '', 1);
		if ($sighting) $pdf->Cell(25, 0, "FOLLOW UP NO.", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "ACTIVITY FOUND", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "AREA OF SIGHTING", 1, 0, 'L', false, '', 1);
		$pdf->Cell(30, 0, "CORRECTIVE ACTION", 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "NEXT FOLLOW UP", 1, 0, 'L', false, '', 1);
		$pdf->Cell(35, 0, "PCO SIGNATURE", 1, 0, 'L', false, '', 1);
		$pdf->Ln();
	}

	public function generateSightingLog($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Visit');
		App::import('Model', 'Media');
		$visits = new Visit();
		$visits->unbindModel(array('hasMany'=>array('ActionLog', 'DeviceService'), 'hasAndBelongsToMany' => array('Chemical', 'Device')));
		$visitData = $visits->find('all', array('conditions'=>array('Client.id'=>$clientId)));
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Sighting Log', $clientId);
		// Set some content to print
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',16);
		$pdf->writeHtml("SIGHTING LOG");
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',10);
		$pdf->Ln();
		$pdf->Cell(20, 0, "DATE", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "TYPE OF PEST", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "AREA OF SIGHTING", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "REPORTED BY", 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "DATE INSPECTED", 1, 0, 'L', false, '', 1);
		$pdf->Cell(30, 0, "CORRECTIVE ACTION", 1, 0, 'L', false, '', 1);
		$pdf->Cell(35, 0, "PCO SIGNATURE", 1, 0, 'L', false, '', 1);
		$pdf->Ln();
		foreach($visitData as $visit) {
			if (sizeof($visit['Sighting']) == 1) {
				$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',10);
				$pdf->Cell(20, 5, substr($visit['Sighting'][0]['dateLogged'], 0, 10), 1, 0, 'L', false, '', 1);
				$pdf->Cell(25, 5, $visit['Sighting'][0]['typeOfPest'], 1, 0, 'L', false, '', 1);
				$pdf->Cell(25, 5, $visit['Sighting'][0]['areaOfSighting'], 1, 0, 'L', false, '', 1);
				$pdf->Cell(25, 5, $visit['Sighting'][0]['reportedBy'], 1, 0, 'L', false, '', 1);
				if (sizeof($visit['ServiceReport']) == 1) $pdf->Cell(20, 5, substr($visit['ServiceReport'][0]['created'], 0, 10), 1, 0, 'L', false, '', 1);
				else $pdf->Cell(20, 5, "", 1, 0, 'L', false, '', 1);
				if ($visit['Sighting'][0]['correctiveAction'] != "null") $pdf->Cell(30, 5, $visit['Sighting'][0]['correctiveAction'], 1, 0, 'L', false, '', 1);
				else $pdf->Cell(30, 5, "", 1, 0, 'L', false, '', 1);
				if (sizeof($visit['ServiceReport']) == 1)  {
					$pdf->SetFontSize(0);
					$image = new Media();
					$image->id = $visit['ServiceReport'][0]['technicianSignature'];
					$pdf->Cell(35, 5, $pdf->Image('@'.$image->field('data'), 170, '', 0, 6, '', '', 'C', false, 300, '', false, false, 0, false, false, false, false, array()), 1, 0, 'L', false, '', 1);
				}
				else $pdf->Cell(35, 5, "", 1, 0, 'L', false, '', 1);
				$pdf->Ln(5);
			}
		}
		$pdf->Output('report.pdf', 'I');
	}

	public function generateChemicalsUsed($clientId) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Client');
		$client = new Client();
		$clientData = $client->find('first', array('conditions'=>array('Client.id'=>$clientId)));
		$chemicals = $clientData['Chemical'];
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Sighting Log', $clientId);
		// Set some content to print
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',16);
		$pdf->writeHtml("CHEMICALS USED ON YOUR PREMISES");
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'B',12);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Cell(55, 0, "CHEMICAL NAME", 1, 0, 'L', false, '', 1);
		$pdf->Cell(70, 0, "ACTIVE INGREDIENTS", 1, 0, 'L', false, '', 1);
		$pdf->Cell(45, 0, "TARGET PESTS", 1, 0, 'L', false, '', 1);
		$pdf->Ln();
		$pdf->SetFont(PDF_FONT_NAME_MAIN,'N',12);
		foreach ($chemicals as $chemical) {
			$pdf->Cell(55, 0, $chemical['name'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(70, 0, $chemical['activeIngredients'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(45, 0, $chemical['targetPests'], 1, 0, 'L', false, '', 1);
			$pdf->Ln();
		}
		$pdf->Output('report.pdf', 'I');
	}

	public function getYearArray() {
		return array('Jan'=>'','Feb'=>'','Mar'=>'','Apr'=>'','May'=>'','Jun'=>'','Jul'=>'','Aug'=>'','Sep'=>'','Oct'=>'','Nov'=>'','Dec'=>'');
	}
}

?>