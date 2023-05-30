<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;

class SignPdf extends Controller
{
    public function sign(Request $req)
    {
        // Load the PDF document
        $pdfPath = Storage::path('public/645e0ce5ac935_7559.pdf');
        $pdf = new Mpdf();
        $pdf->SetSourceFile($pdfPath);
        $pageCount = $pdf->SetSourceFile($pdfPath);
        $tplId = $pdf->ImportPage($pageCount);

        // Set the signer's name, location, and reason
        $signerName = 'Your Name';
        $signerLocation = 'Your Location';
        $signerReason = 'Your Reason';

        // Set the signature appearance
        $signatureAppearance = [
            'Name' => $signerName,
            'Location' => $signerLocation,
            'Reason' => $signerReason,
            'ContactInfo' => '',
            'Font' => 'helvetica',
            'FontSize' => 12,
        ];

        // Sign the PDF
        $certificatePath = Storage::path('public/sslmsu2022.pdf');
        $certificatePassword = 'ccmsu2022';

        //$pdf->SetImportUse();

        $pdf->UseTemplate($tplId);

        $pdf->SetSignature(
            $certificatePath,
            $certificatePassword,
            '',
            '',
            2,
            $signatureAppearance
        );

        // Generate the signed PDF
        $signedPdfPath = Storage::path('public/sign_645e0ce5ac935_7559.pdf');
        $pdf->Output($signedPdfPath, 'F');

    }
}
