<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF;
use phpseclib\Crypt\RSA;

class WordsToPdf extends Controller
{
    public function getWord(Request $req)
    {
        $templateFile = Storage::path('public/contract-ft-scopus.docx');
        $templateProcessor = new TemplateProcessor($templateFile);

        // Define the data to be merged from JSON
        $data = array(
            'auther_name' => 'อัครรินทร์ บุปผา'
        );

        // Replace the MERGEFIELD field with the actual data from JSON
        $templateProcessor->setValue('auther_name', $data['auther_name']);

        // Save the merged Word document to a new file
        $outputFile = Storage::path('public/FastTrack_out.docx');
        $templateProcessor->saveAs($outputFile);
    }

    public function testLoop(Request $req)
    {
        $templateFile = Storage::path('public/docx-template.docx');
        $templateProcessor = new TemplateProcessor($templateFile);

        // Define the data to be merged from JSON
        $data = array(
            'auther_name' => 'อัครรินทร์ บุปผา'
        );

        // Replace the MERGEFIELD field with the actual data from JSON
        $templateProcessor->setValue('FacultyName', $data['auther_name']);

        $ar = array([
            "block_stg"=>"",
            "stgname"=>"\${stgname_b1}",
            "kpiname"=>"\${kpiname_b1}",
            "projname"=>"\${projname_b1}",
            "block_aim"=>"\${block_aim_b1}",
            "/block_aim"=>"\${/block_aim_b1}",
            "aimname"=>"\${aimname_b1}",
            "block_kpi"=>"\${block_kpi_b1}",
            "/block_kpi"=>"\${/block_kpi_b1}",
            "kpiname"=>"\${kpiname_b1}",
            "block_proj"=>"\${block_proj_b1}",
            "/block_proj"=>"\${/block_proj_b1}",
        ],[
            "block_stg"=>"",
            "stgname"=>"\${stgname_b2}",
            "kpiname"=>"\${kpiname_b2}",
            "projname"=>"\${projname_b2}",
            "block_aim"=>"\${block_aim_b2}",
            "/block_aim"=>"\${/block_aim_b2}",
            "aimname"=>"\${aimname_b1}",
            "block_kpi"=>"\${block_kpi_b2}",
            "/block_kpi"=>"\${/block_kpi_b2}",
            "kpiname"=>"\${kpiname_b2}",
            "block_proj"=>"\${block_proj_b2}",
            "/block_proj"=>"\${/block_proj_b2}",
        ]);

        $ar2 = array([
            "block_proj_b1"=>"\${block_proj_b1_l1}",
            "/block_proj_b1"=>"\${/block_proj_b1_l1}",
        ],[
            "block_proj_b1"=>"\${block_proj_b1_l2}",
            "/block_proj_b1"=>"\${/block_proj_b1_l2}",
        ]);

        $templateProcessor->cloneBlock('block_stg', count($ar), true, false, $ar);

        $templateProcessor->cloneBlock('block_proj_b1', count($ar2), true, false, $ar2);
        // $i = 0;
        // foreach ($ar as $key => $value) {
        //     $i++;
        //     foreach ($value as $kx => $v) {
        //         $templateProcessor->setValue($kx.'#'.$i, $v);
        //     }
        // }

        // Save the merged Word document to a new file
        $outputFile = Storage::path('public/docx-out.docx');
        $templateProcessor->saveAs($outputFile);
    }

    public function createContract(Request $req)
    {
        $templateFile = Storage::path('public/contract-ft-scopus.docx');
        $templateProcessor = new TemplateProcessor($templateFile);

        // Define the data to be merged from JSON
        $data = array(
            'auther_name' => 'อัครรินทร์ บุปผา'
        );

        // Replace the MERGEFIELD field with the actual data from JSON
        $templateProcessor->setValue('auther_name', $data['auther_name']);

        // Save the merged Word document to a new file
        $randomName = uniqid() . '_' . mt_rand(1000, 9999);
        $outputFile = Storage::path('public/'.$randomName.'.docx');
        $templateProcessor->saveAs($outputFile);
        
        $url = 'http://10.11.1.243/doc2pdf/api/rpc'; // URL where you want to send the file

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'fileupload' => curl_file_create($outputFile)
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        // return $response;
        // Handle the response as needed
        $outputPdf = Storage::path('public/'.$randomName.'.pdf');
        $result = file_put_contents($outputPdf, $response);
        if ($result !== false) {
            return response()->file($outputPdf);
        }else{
            $resj = array(
                "res" => 0,
                "msg" => "file error."
            );
            return response()->json($resj);
        }
    }

    public function sign(Request $req)
    {
        // Generate the PDF document
        // $pdf = new TCPDF();
        // $pdf->AddPage();
        // $pdf->SetFont('helvetica', '', 12);
        // $pdf->Cell(0, 10, 'Hello World!', 0, 1, 'C');
        // $pdfContent = $pdf->Output('pdf_file.pdf', 'S');

        // Prepare the digital certificate
        $certificatePath = Storage::path('public/certificate.pem');
        $privateKeyPath = Storage::path('public/private_key.pem');
        $privateKeyPassword = ''; // If applicable

        // Load the PDF document
        $pdf_file = Storage::path('public/contract-ft-scopus.pdf');

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($pdf_file);
        $pageId = $pdf->importPage(1);

        // Add a digital signature field
        $pdf->AddPage();
        $pdf->useTemplate($pageId, 10, 10, 190);
        $signatureFieldName = 'signature_field';
        $pdf->setSignature($certificatePath, $privateKeyPath, $privateKeyPassword, '', 2, $info = ['Name' => $signatureFieldName]);

        // Sign the PDF using the digital certificate
        // $rsa = new RSA();
        $rsa = new \phpseclib\Crypt\RSA(); 
        $certificateContent = file_get_contents($certificatePath);
        $privateKeyContent = file_get_contents($privateKeyPath);
        $rsa->loadKey($privateKeyContent);
        $pdf->setSignatureAppearance(10, 10, 40, 15);
        $signature = $pdf->getLastSignature();
        $certificate = $signature['certificate'];
        $signedData = $pdf->getSignatureData();
        $signatureData = $rsa->sign($signedData);
        $pdf->setSignature($certificate, $privateKeyContent, $signatureData, '');

        // Output the signed PDF
        $signed_pdf_file = Storage::path('public/signed_pdf_file.pdf');
        $pdf->Output($signed_pdf_file, 'F');

    }
}
