<?php
App::uses('AppModel', 'Model');
/**
 * Document Model
 *
 * @property DocumentCategory $DocumentCategory
 * @property User $User
 * @property Technician $Technician
 * @property Client $Client
*/
class Document extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';
	public $actsAs = array( 'AuditLog.Auditable' ); //makes this model auditable

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
			),
			'User' => array(
					'className' => 'User',
					'foreignKey' => 'user_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
			'Company' => array(
					'className' => 'Company',
					'foreignKey' => 'company_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	*/
	public $hasMany = array(
			'Technician' => array(
					'className' => 'Technician',
					'foreignKey' => 'document_id',
					'dependent' => false,
					'conditions' => '',
					'fields' => '',
					'order' => '',
					'limit' => '',
					'offset' => '',
					'exclusive' => '',
					'finderQuery' => '',
					'counterQuery' => ''
			)
	);


	/**
	 * hasAndBelongsToMany associations
	 *
	 * @var array
	*/
	public $hasAndBelongsToMany = array(
			'Client' => array(
					'className' => 'Client',
					'joinTable' => 'clients_documents',
					'foreignKey' => 'document_id',
					'associationForeignKey' => 'client_id',
					'unique' => 'keepExisting',
					'conditions' => '',
					'fields' => '',
					'order' => '',
					'limit' => '',
					'offset' => '',
					'finderQuery' => '',
			)
	);

	function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		// $bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	public function getAllDocumentsForClient($clientId, $companyId = -1, $documentId = -1) {
		if ($documentId == -1) $documentQuery = "";
		else $documentQuery = "WHERE `id` = " . $documentId;
		return $this->query(
				"SELECT * FROM (
				SELECT `documents`.`id`, `documents`.`modified`, `documents`.`file_type`, `documents`.`created`, `documents`.`size`, `documents`.`name`, `documents`.`order` AS `docOrder`, `document_categories`.`order` AS `catOrder`, `document_categories`.`name` AS `catName` FROM `documents`
				LEFT JOIN  `document_categories` ON (`document_categories`.`id` = `documents`.`document_category_id`)
				WHERE `visibleToAll` = 1 AND `documents`.`archived` = 0 AND (`documents`.`company_id` = -1 OR `documents`.`company_id` = $companyId)
				UNION DISTINCT
				SELECT `documents`.`id`, `documents`.`modified`, `documents`.`file_type`, `documents`.`created`, `documents`.`size`, `documents`.`name`, `documents`.`order` AS `docOrder`, `document_categories`.`order` AS `catOrder`, `document_categories`.`name` AS `catName` FROM `documents`
				LEFT JOIN  `document_categories` ON (`document_categories`.`id` = `documents`.`document_category_id`)
				LEFT JOIN `clients_documents` ON (`clients_documents`.`document_id` = `documents`.`id`)
				WHERE `clients_documents`.`client_id` = $clientId AND `documents`.`archived` = 0 AND (`documents`.`company_id` = -1 OR `documents`.`company_id` = $companyId)
				UNION DISTINCT
				SELECT `documents`.`id`, `documents`.`modified`, `documents`.`file_type`, `documents`.`created`, `documents`.`size`, `documents`.`name`, `documents`.`order` AS `docOrder`, `document_categories`.`order` AS `catOrder`, `document_categories`.`name` AS `catName` FROM `documents`
				LEFT JOIN  `document_categories` ON (`document_categories`.`id` = `documents`.`document_category_id`)
				LEFT JOIN `chemicals` ON (`chemicals`.`document_id` = `documents`.`id`)
				LEFT JOIN `chemicals_clients` ON (`chemicals_clients`.`chemical_id` = `chemicals`.`id`)
				WHERE `chemicals_clients`.`client_id` = $clientId AND `documents`.`archived` = 0 AND (`documents`.`company_id` = -1 OR `documents`.`company_id` = $companyId)
				UNION DISTINCT
				SELECT `documents`.`id`, `documents`.`modified`, `documents`.`file_type`, `documents`.`created`, `documents`.`size`, `documents`.`name`, `documents`.`order` AS `docOrder`, `document_categories`.`order` AS `catOrder`, `document_categories`.`name` AS `catName` FROM `documents`
				LEFT JOIN  `document_categories` ON (`document_categories`.`id` = `documents`.`document_category_id`)
				LEFT JOIN `policies` ON (`policies`.`document_id` = `documents`.`id`)
				LEFT JOIN `clients_policies` ON (`clients_policies`.`policy_id` = `policies`.`id`)
				WHERE `clients_policies`.`client_id` = $clientId AND `documents`.`archived` = 0 AND (`documents`.`company_id` = -1 OR `documents`.`company_id` = $companyId)
				UNION DISTINCT
				SELECT CONCAT('R', `reports`.`id`), '', `reports`.`function`, '', '-555', `reports`.`name`, `reports`.`order`, `document_categories`.`order` AS `catOrder`, `document_categories`.`name` AS `catName` FROM `reports`
				LEFT JOIN  `document_categories` ON (`document_categories`.`id` = `reports`.`document_category_id`)
				UNION DISTINCT
				SELECT `documents`.`id`, `documents`.`modified`, `documents`.`file_type`, `documents`.`created`, `documents`.`size`, `documents`.`name`, `documents`.`order` AS `docOrder`, `document_categories`.`order` AS `catOrder`, `document_categories`.`name` AS `catName` FROM `documents`
				LEFT JOIN  `document_categories` ON (`document_categories`.`id` = `documents`.`document_category_id`)
				LEFT JOIN  `technicians` ON (`technicians`.`document_id` = `documents`.`id`) AND (`documents`.`company_id` = -1 OR `documents`.`company_id` = $companyId)
				LEFT JOIN `visits` ON (`visits`.`technician_id` = `technicians`.`id`)
				WHERE `visits`.`client_id` = $clientId  AND `documents`.`archived` = 0
		) AS all_client_documents
				" . $documentQuery . " 
				GROUP BY  `id`
				ORDER BY `catOrder` ASC, `docOrder` ASC, `modified` DESC");
	}

	public function isDocumentVisibleToClient($clientId, $documentId) {
		/*$result = $this->query(
				"SELECT  `Document`.`id` ,  `Document`.`name` ,  `Document`.`filename` ,  `Document`.`file_type` ,  `Document`.`document_category_id` ,  `Document`.`order` ,  `Document`.`mime` ,  `Document`.`meta` ,  `Document`.`size` ,  `Document`.`visibleToAll` ,  `Document`.`active` ,  `Document`.`archived` ,  `Document`.`created` ,  `Document`.`modified` , `Document`.`user_id` ,  `ClientsDocument`.`id` ,  `ClientsDocument`.`client_id` ,  `ClientsDocument`.`document_id`
				FROM  `documents` AS  `Document`
				JOIN  `clients_documents` AS  `ClientsDocument` ON (`ClientsDocument`.`client_id` = $clientId)
				WHERE (`ClientsDocument`.`document_id` =  `Document`.`id` OR `Document`.`visibleToAll` = 1)
				AND `Document`.`active` = 1
				AND `Document`.`archived` = 0
				AND `Document`.`id` = $documentId
				GROUP BY  `Document`.`id`"); */
		$result = $this->getAllDocumentsForClient($clientId, $documentId=$documentId);
		if(count($result) == 0) return false;
		else return true;
	}

	//This function will generate a new service report xls document and return the document_id
	public function generateServiceReport($visitId, $clientId, $technicianUserId, $technicianSignature, $clientSignature) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		App::import('Model', 'Visit');
		App::import('Model', 'ClientsDocument');
		//get visit information and generate report data
		$Visit = new Visit();
		$ClientsDocument = new ClientsDocument();
		$Visit->id = $visitId;
		$data = $Visit->find('first', array('conditions' => array('Visit.id =' => $visitId)));
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Service Report', $clientId);
		$filename = time() . '-' . $visitId . '-ServiceReport.pdf';
		// Set some content to print
		$this->writeServiceReport($pdf, $data, $technicianSignature, $clientSignature);
		// This method has several options, check the source code documentation for more information.
		$pdf->Output(PDF_FILE_LOCATION . $filename, 'F');
		$this->create();
		$this->set('name', 'Service Report (' . date('j F Y') . ')');
		$this->set('filename', $filename);
		$this->set('file_type', 'PDF Document');
		$this->set('document_category_id', 3);
		$this->set('mime', 'application/pdf');
		$this->set('size', filesize(PDF_FILE_LOCATION . $filename));
		$this->set('editable', 0);
		$this->set('policyDocument', 1);
		$this->set('user_id', $technicianUserId);
		$this->save();
		$docId = $this->field('id');
		$ClientsDocument->create();
		$ClientsDocument->set('client_id', $clientId);
		$ClientsDocument->set('document_id', $docId);
		$ClientsDocument->save();
		return $docId;
	}

	public function writeServiceReport($pdf, $data, $technicianSignature, $clientSignature) {
		App::import('Model', 'Media');

		$pdf->SetFontSize(16);
		$pdf->Cell(115, 18, "SERVICE REPORT", 0, 0, 'R', false, '', 1);
		$pdf->SetFontSize(10);
		$pdf->Cell(35, 18, "No.", 0, 0, 'R', false, '', 1);
		$pdf->SetFontSize(16);
		$pdf->Cell(35, 18, $data['Visit']['id'], 0, 0, 'L', false, '', 1);
		$pdf->Ln();
		$pdf->SetFontSize(10);

		//information header
		$phpdate = strtotime($data['Visit']['timeStarted']);
		$reason = $data['Visit']['purpose'];
		$startTime = date('H:i', $phpdate);
		$address = preg_replace( "/\r|\n/", " ", $data['Client']['address']);
		$addressLine1 = $addressLine2 = "";
		if (strlen($address) > 80) {
			$midpoint = round(strlen($address) / 2) + stripos(substr($address, round(strlen($address) / 2)),  ' ');
			$addressLine1 = substr($address, 0, $midpoint);
			$addressLine2 = substr($address, $midpoint + 1);
		}
		else {
			if (strlen($address) > 40)  {
				$breakpoint = strripos(substr($address, 0, 40),  ' ');
				$addressLine1 = substr($address, 0, $breakpoint);
				$addressLine2 = substr($address, $breakpoint + 1);
			}
			else $addressLine1 = $address;
		}
		$pdf->Cell(25, 0, "Date", 0, 0, 'L', false, '', 1);
		$pdf->Cell(40, 0, date ("j F Y"), 0, 0, 'L', false, '', 1);
		$pdf->Ln();
		$pdf->Cell(25, 0, "Client", 0, 0, 'L', false, '', 1);
		$pdf->Cell(60, 0, $data['Client']['name'], 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "Departure", 0, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "km", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "time", 1, 0, 'L', false, '', 1);
		$pdf->Cell(30, 0, "Site time in   ".$startTime, 1, 0, 'L', false, '', 1);
		$pdf->Ln();
		$pdf->Cell(25, 0, "Address", 0, 0, 'L', false, '', 1);
		$pdf->Cell(60, 0, $addressLine1, 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "Arrival", 0, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "km", 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "time", 1, 0, 'L', false, '', 1);
		$pdf->Cell(30, 0, "Site time out   ".date ("H:i"), 1, 0, 'L', false, '', 1);
		$pdf->Ln();
		$pdf->Cell(25, 0, "", 0, 0, 'L', false, '', 1);
		$pdf->Cell(60, 0, $addressLine2, 1, 0, 'L', false, '', 1);
		$pdf->Cell(20, 0, "", 0);
		if ($reason == 'installation') $pdf->Cell(25, 0, "Initial Service", 1, 0, 'L', false, '', 1);
		else if ($reason == 'routine_visit') $pdf->Cell(25, 0, "Routine Service", 1, 0, 'L', false, '', 1);
		else $pdf->Cell(30, 0, "Additional Service", 1, 0, 'L', false, '', 1);
		$pdf->Ln();
		$pdf->Ln();
		
		//problems table
		$pdf->Cell(10, 0, "", 1, 0, 'R', false, '', 1);
		$pdf->Cell(45, 0, "AREAS/DEPT", 1, 0, 'C', false, '', 1);
		$pdf->Cell(15, 0, "INS", 1, 0, 'C', false, '', 1);
		$pdf->Cell(15, 0, "TRET", 1, 0, 'C', false, '', 1);
		$pdf->Cell(15, 0, "PEST", 1, 0, 'C', false, '', 1);
		$pdf->Cell(12, 0, "Y", 1, 0, 'C', false, '', 1);
		$pdf->Cell(12, 0, "N", 1, 0, 'C', false, '', 1);
		$pdf->Cell(61, 0, "REMARKS", 1, 0, 'C', false, '', 1);
		$pdf->Ln();
		$i = 1;
		foreach ($data['AreaSummary'] as $summary) {
			$pdf->Cell(10, 0, "".$i, 1, 0, 'C', false, '', 1);
			$pdf->Cell(45, 0, $summary['area'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(15, 0, ($summary['inspected'] == 1 ? "X" : ""), 1, 0, 'C', false, '', 1);
			$pdf->Cell(15, 0, ($summary['treated'] == 1 ? "X" : ""), 1, 0, 'C', false, '', 1);
			$pdf->Cell(15, 0, $summary['pestActivityType'], 1, 0, 'C', false, '', 1);
			$pdf->Cell(12, 0, ($summary['pestActivity'] == 1 ? "X" : ""), 1, 0, 'C', false, '', 1);
			$pdf->Cell(12, 0, ($summary['pestActivity'] == 1 ? "" : "X"), 1, 0, 'C', false, '', 1);
			$pdf->Cell(61, 0, $summary['comments'], 1, 0, 'L', false, '', 1);
			$pdf->Ln();
			$i++;
		}
		while ($i < 5) {
			$pdf->Cell(10, 0, "".$i, 1, 0, 'C', false, '', 1);
			$pdf->Cell(45, 0, "", 1, 0, 'L', false, '', 1);
			$pdf->Cell(15, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Cell(15, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Cell(15, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Cell(12, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Cell(12, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Cell(61, 0, "", 1, 0, 'L', false, '', 1);
			$pdf->Ln();
			$i++;
		}
		$pdf->Ln();
		
		//chemicals table
		$pdf->Cell(9, 0, "", 1, 0, 'C', false, '', 1);
		$pdf->Cell(44, 0, "CHEMICAL", 1, 0, 'C', false, '', 1);
		$pdf->Cell(44, 0, "REG. No. ACT 36/1947", 1, 0, 'C', false, '', 1);
		$pdf->Cell(44, 0, "DILUTION RATE", 1, 0, 'C', false, '', 1);
		$pdf->Cell(44, 0, "QUANTITY USED", 1, 0, 'C', false, '', 1);
		$pdf->Ln();
		$i = 1;
		foreach ($data['Chemical'] as $chemical) {
			$pdf->Cell(9, 0, "".$i, 1, 0, 'C', false, '', 1);
			$pdf->Cell(44, 0, $chemical['name'], 1, 0, 'L', false, '', 1);
			$pdf->Cell(44, 0, $chemical['code'], 1, 0, 'C', false, '', 1);
			$pdf->Cell(44, 0, $chemical['ChemicalsVisit']['dilution'], 1, 0, 'C', false, '', 1);
			$pdf->Cell(44, 0, $chemical['ChemicalsVisit']['dosage'], 1, 0, 'C', false, '', 1);
			$pdf->Ln();
			$i++;
		}
		//fill up with some blanks if it's too short
		while ($i < 5) {
			$pdf->Cell(9, 0, "".$i, 1, 0, 'C', false, '', 1);
			$pdf->Cell(44, 0, "", 1, 0, 'L', false, '', 1);
			$pdf->Cell(44, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Cell(44, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Cell(44, 0, "", 1, 0, 'C', false, '', 1);
			$pdf->Ln();
			$i++;
		}
		$pdf->Ln();
		
		//serviceman's recommendation table
		$pdf->SetFontSize(13);
		$pdf->Write(0, "SERVICEMAN'S RECOMMENDATIONS", '', 0, 'C', true, 0, false, false, 0);
		$pdf->SetFontSize(10);
		$pdf->Cell(35, 0, "HOUSEKEEPING", 1, 0, 'L', false, '', 1);
		$pdf->Cell(150, 0, $data['Visit']['recommendationHousekeeping'], 1);
		$pdf->Ln();
		$pdf->Cell(35, 0, "STACKING", 1, 0, 'L', false, '', 1);
		$pdf->Cell(150, 0, $data['Visit']['recommendationStacking'], 1);
		$pdf->Ln();
		$pdf->Cell(35, 0, "PROOFING", 1, 0, 'L', false, '', 1);
		$pdf->Cell(150, 0, $data['Visit']['recommendationProofing'], 1);
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->Write(0, "CLIENT'S RATING: " . $data['Visit']['clientRating'], '', 0, 'L', true, 0, false, false, 0);
		$pdf->Write(0, 'REMARKS: ' . ($data['Visit']['clientRemarks'] == null ? "None." : $data['Visit']['clientRemarks']), '', 0, 'L', true, 0, false, false, 0);
		$pdf->Ln();
		$pdf->Ln();
		
		//signatures table
		$image = new Media();
		$image->id = $clientSignature;
		$pdf->Cell(25, 0, "CUSTOMER", 0);
		$pdf->Cell(70, 0, $data['Client']['primaryContact'], 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "SIGNATURE", 0, 0, 'L', false, '', 1);
		$pdf->SetFontSize(0);
		$pdf->Cell(65, 15, $pdf->Image('@'.$image->field('data'), 150, '', 29, 0, '', '', 'C', false, 300, '', false, false, 0, false, false, false, false, array()), 1, 0, 'C', false, '', 1);
		$pdf->SetFontSize(10);
		$pdf->Ln();
		$image->id = $technicianSignature;
		$pdf->Cell(25, 0, "SERVICEMAN", 0);
		$pdf->Cell(70, 0, $data['Technician']['name'], 1, 0, 'L', false, '', 1);
		$pdf->Cell(25, 0, "SIGNATURE", 0, 0, 'L', false, '', 1);
		$pdf->SetFontSize(0);
		$pdf->Cell(65, 15, $pdf->Image('@'.$image->field('data'), 150, '', 29, 0, '', '', 'C', false, 300, '', false, false, 0, false, false, false, false, array()), 1, 0, 'C', false, '', 1);
		$pdf->SetFontSize(10);
		$pdf->Ln();
		$pdf->Cell(45, 0, "PCO'S REGISTRATION No.", 0);
		$pdf->Cell(45, 0, $data['Technician']['regNo'], 1, 0, 'L', false, '', 1);
	}

	private function setupPDFDocument($pdf, $title, $clientId) {
		App::import('Model', 'Client');
		$Client = new Client();
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle('Index File');

		// set default header data
		$pdf->SetHeaderData("@".$Client->getClientCompanyHeader($clientId), 150, '', '', array(0,64,255), array(0,64,128));
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

	public function getIndexFile($clientId, $companyId = -1) {
		$documentList = $this->getAllDocumentsForClient($clientId, $companyId);
		App::import('Vendor', 'tcpdf_min/tcpdf');
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, 'Document Index', $clientId);
		// Set some content to print
		$currentCat = "empty";
		$counta = 1;
		$countb = 1;
		$pdf->SetFontSize(22);
		$pdf->write(0,"INDEX", '', false, 'C');
		$pdf->SetFontSize(14);
		$pdf->ln();
		$pdf->ln();
		$pdf->writeHTML("Below is a list of all documents present in the client file. You can access each of these documents directly from the client portal by navigating to the relevant section.<br>");
		foreach ($documentList as $document)  {
			if ($document['all_client_documents']['catName'] != $currentCat) {
				$currentCat = $document['all_client_documents']['catName'];
				$pdf->writeHTML("<br>");
				$pdf->writeHTML("<h3>" . $counta . ".  $currentCat </h3>");
				$countb=1;
				$counta++;
			}		
			$docName = $document['all_client_documents']['name'];
			$pdf->writeHTML("&nbsp;&nbsp;" . ($counta-1) . "." . $countb . ". $docName");
			$countb++;
		}
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('index.pdf', 'I');
	}
	
	public function outputTemplatedFile($clientId, $text=null, $title=null) {
		App::import('Vendor', 'tcpdf_min/tcpdf');
		if ($text == null) {
			$text = file_get_contents('files/'.Configure::read('file_upload_prefix').$this->field("filename"));
			$title = $this->field("name");
		}
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = $this->setupPDFDocument($pdf, $title, $clientId);
		// Set some content to print
		$pdf->writeHTML($text);
		// This method has several options, check the source code documentation for more information.
		$pdf->Output($this->sanitize_file_name($title).'.pdf', 'I');
	}

	function sanitize_file_name($filename) {
		$clean_name = strtr($filename, 'ŠŽšžŸÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
		$clean_name = strtr($clean_name, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', 'Œ' => 'OE', 'œ' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
		$clean_name = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $clean_name);
		return $clean_name;
	}
	
	function random_string($length) {
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));
	
		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}
	
		return $key;
	}
}