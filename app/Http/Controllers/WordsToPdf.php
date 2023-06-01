<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

        $jsonData = Storage::get('public/report.json');

        $jsonData = json_decode($jsonData);
        
        $ar = array();
        $aim = array();
        $kpi = array();
        $proj = array();
        foreach ($jsonData as $key => $val) {
            $a = [
                "block_stg"=>"",
                "stgname"=>"\${stgname_".$key."}",
                "kpiname"=>"\${kpiname_".$key."}",
                "projname"=>"\${projname_".$key."}",
                "sumexp1"=>"\${sumexp1_".$key."}",
                "sumexp2"=>"\${sumexp2_".$key."}",
                "sumexp3"=>"\${sumexp3_".$key."}",
                "sumexp4"=>"\${sumexp4_".$key."}",
                "sumexp5"=>"\${sumexp5_".$key."}",
                "sumall"=>"\${sumall_".$key."}",
                "indname"=>"\${indname_".$key."}",
                "indunit"=>"\${indunit_".$key."}",
                "indgoal"=>"\${indgoal_".$key."}",
                "block_aim"=>"\${block_aim_".$key."}",
                "/block_aim"=>"\${/block_aim_".$key."}",
                "aimname"=>"\${aimname_".$key."}",
                "block_kpi"=>"\${block_kpi_".$key."}",
                "/block_kpi"=>"\${/block_kpi_".$key."}",
                "kpiname"=>"\${kpiname_".$key."}",
                "block_proj"=>"\${block_proj_".$key."}",
                "/block_proj"=>"\${/block_proj_".$key."}",
            ];
            array_push($ar,$a);

            $aim[$key] = array();
            foreach ($val->AIMS as $k => $vx) {
                $ax = [
                    "block_aim_".$key=>"",
                    "aimname_".$key=>"\${aimname_".$key."_".$k."}"
                ];
                array_push($aim[$key],$ax);
            }

            $kpi[$key] = array();
            foreach ($val->INDS as $k => $vx) {
                $ax = [
                    "block_kpi_".$key=>"",
                    "kpiname_".$key=>"\${kpiname_".$key."_".$k."}"
                ];
                array_push($kpi[$key],$ax);
            }

            $proj[$key] = array();
            foreach ($val->PROJS as $k => $vx) {
                $ax = [
                    "block_proj_".$key=>"",
                    "projname_".$key=>"\${projname_".$key."_".$k."}",
                    "sumexp1_".$key=>"\${sumexp1_".$key."_".$k."}",
                    "sumexp2_".$key=>"\${sumexp2_".$key."_".$k."}",
                    "sumexp3_".$key=>"\${sumexp3_".$key."_".$k."}",
                    "sumexp4_".$key=>"\${sumexp4_".$key."_".$k."}",
                    "sumexp5_".$key=>"\${sumexp5_".$key."_".$k."}",
                    "sumall_".$key=>"\${sumall_".$key."_".$k."}",
                    "indname_".$key=>"indname_".$key."_".$k."",
                    "indunit_".$key=>"indunit_".$key."_".$k."",
                    "indgoal_".$key=>"indgoal_".$key."_".$k.""
                ];
                array_push($proj[$key],$ax);
            }
        } 

        //return $proj;

        $templateProcessor->cloneBlock('block_stg', count($ar), true, false, $ar);
        
        foreach ($jsonData as $key => $val) {
            $templateProcessor->setValue("stgname_".$key, $val->STRATEGICCODE." ".$val->STRATEGICNAME);
            $templateProcessor->cloneBlock("block_aim_".$key, count($aim[$key]), true, false, $aim[$key]);
            foreach ($aim[$key] as $ka => $va) {
                var_dump($va["aimname_".$key]);
                var_dump($val->AIMS[$ka]->AIM_NAME);
                $templateProcessor->setValue($va["aimname_".$key], $val->AIMS[$ka]->AIM_NAME);
            }

            $templateProcessor->cloneBlock("block_kpi_".$key, count($kpi[$key]), true, false, $kpi[$key]);
            foreach ($kpi[$key] as $ka => $va) {
                var_dump($va["kpiname_".$key]);
                var_dump($val->INDS[$ka]->IND_NAME);
                $templateProcessor->setValue($va["kpiname_".$key], $val->INDS[$ka]->IND_NAME);
            }

            $templateProcessor->cloneBlock("block_proj_".$key, count($proj[$key]), true, false, $proj[$key]);
            foreach ($proj[$key] as $ka => $va) {
                var_dump($va["projname_".$key]);
                var_dump($val->PROJS[$ka]->PROJECTWORKNAME);
                var_dump($va["sumexp1_".$key]);
                var_dump($val->PROJS[$ka]->SUMAMOUNTEXP1);
                var_dump($va["sumexp2_".$key]);
                var_dump($val->PROJS[$ka]->SUMAMOUNTEXP2);
                var_dump($va["sumexp3_".$key]);
                var_dump($val->PROJS[$ka]->SUMAMOUNTEXP3);
                var_dump($va["sumexp4_".$key]);
                var_dump($val->PROJS[$ka]->SUMAMOUNTEXP4);
                var_dump($va["sumexp5_".$key]);
                var_dump($val->PROJS[$ka]->SUMAMOUNTEXP5);
                var_dump($va["sumall_".$key]);
                var_dump($val->PROJS[$ka]->AMOUNT);
                var_dump($va["indname_".$key]);
                var_dump($va["indunit_".$key]);
                var_dump($va["indgoal_".$key]);
                var_dump($val->PROJS[$ka]->GOALKPIS);
                $templateProcessor->setValue($va["projname_".$key], $val->PROJS[$ka]->PROJECTWORKNAME);
                $templateProcessor->setValue($va["sumexp1_".$key], number_format($val->PROJS[$ka]->SUMAMOUNTEXP1));
                $templateProcessor->setValue($va["sumexp2_".$key], number_format($val->PROJS[$ka]->SUMAMOUNTEXP2));
                $templateProcessor->setValue($va["sumexp3_".$key], number_format($val->PROJS[$ka]->SUMAMOUNTEXP3));
                $templateProcessor->setValue($va["sumexp4_".$key], number_format($val->PROJS[$ka]->SUMAMOUNTEXP4));
                $templateProcessor->setValue($va["sumexp5_".$key], number_format($val->PROJS[$ka]->SUMAMOUNTEXP5));
                $templateProcessor->setValue($va["sumall_".$key], number_format($val->PROJS[$ka]->AMOUNT));

                $data = [];
                foreach ($val->PROJS[$ka]->GOALKPIS as $kgp => $vkgp) {
                    $szx = [ 
                        $va["indname_".$key] => $vkgp->INDNAME,
                        $va["indunit_".$key] => $vkgp->INDUNIT,
                        $va["indgoal_".$key] => $vkgp->INDGOAL
                    ];
                    array_push($data, $szx);
                }

                var_dump("dddddddddddddddddddddddddddddddddddddddddddddddd",$data);

                foreach ($val->PROJS[$ka]->GOALKPIS as $data) {
                    $templateProcessor->cloneRow($va["indname_".$key], $data);
                }
            }


        }
        

        // Save the merged Word document to a new file
        // $outputFile = Storage::path('public/docx-out.docx');
        // $templateProcessor->saveAs($outputFile);
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
