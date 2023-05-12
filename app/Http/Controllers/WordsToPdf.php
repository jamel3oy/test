<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class WordsToPdf extends Controller
{
    public function getWord(Request $req)
    {
        $templateFile = Storage::path('public/FastTrack.docx');
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

    public function createContract(Request $req)
    {
        $templateFile = Storage::path('public/FastTrack.docx');
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
}
