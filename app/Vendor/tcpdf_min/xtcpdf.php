<?php 
App::import('Vendor','tcpdf_min/tcpdf'); 

class XTCPDF  extends TCPDF 
{ 

    //var $xheadertext  = 'PDF created using CakePHP and TCPDF'; 
    //v//ar $xheadercolor = array(0,0,200); 
    //var $xfootertext  = 'Copyright © %d XXXXXXXXXXX. All rights reserved.'; 
    //var $xfooterfont  = PDF_FONT_NAME_MAIN ; 
    //var $xfooterfontsize = 8 ; 


    /** 
    * Overwrites the default header 
    * set the text in the view using 
    *    $fpdf->xheadertext = 'YOUR ORGANIZATION'; 
    * set the fill color in the view using 
    *    $fpdf->xheadercolor = array(0,0,100); (r, g, b) 
    * set the font in the view using 
    *    $fpdf->setHeaderFont(array('YourFont','',fontsize)); 
    */ 
    function Header() { 
        $image_file = PDF_HEADER_LOGO;
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 60);
        // Title
        $this->Cell(0, 15, 'Document Index', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    } 

    /** 
    * Overwrites the default footer 
    * set the text in the view using 
    * $fpdf->xfootertext = 'Copyright © %d YOUR ORGANIZATION. All rights reserved.'; 
    
    function Footer() 
    { 
        $year = date('Y'); 
        $footertext = sprintf($this->xfootertext, $year); 
        $this->SetY(-20); 
        $this->SetTextColor(0, 0, 0); 
        $this->SetFont($this->xfooterfont,'',$this->xfooterfontsize); 
        $this->Cell(0,8, $footertext,'T',1,'C'); 
    } 
    */ 
} 
?>