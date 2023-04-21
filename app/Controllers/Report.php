<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\UserEnrolmentCourse;


class Report extends BaseController
{
    public function exportToExcel()
    {
        // Load the table view into a variable
$html = $this->load->view('report_result');

// Put the html into a temporary file
$tmpfile = time().'.html';
file_put_contents($tmpfile, $html);

// Read the contents of the file into PHPExcel Reader class
$reader = new PHPExcel_Reader_HTML; 
$content = $reader->load($tmpfile); 

// Pass to writer and output as needed
$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
$objWriter->save('excelfile.xlsx');

// Delete temporary file
unlink($tmpfile);
    }

}