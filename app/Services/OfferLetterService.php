<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\OfferLetter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferLetterService
{
    public function generateOfferLetter(JobApplication $application, array $data): OfferLetter
    {
        $vacancy = $application->vacancy;
        $designationName = $vacancy->designation->name ?? 'N/A';
        $positionName = $vacancy->position->name ?? 'N/A';

        $fileName = 'offer_letters/offer_' . $application->application_number . '_' . time() . '.html';

        $htmlContent = view('offer-letters.letter', [
            'date' => date('d M Y'),
            'applicationNumber' => $application->application_number,
            'recipientName' => $application->full_name,
            'recipientEmail' => $application->email,
            'positionName' => $positionName,
            'designationName' => $designationName,
            'salary' => $data['salary'],
            'benefits' => $data['benefits'],
            'reportingDate' => date('d M Y', strtotime($data['reporting_date'])),
            'employmentTerms' => $data['employment_terms'],
            'sigFileName' => null,
            'signedDate' => null,
        ])->render();

        Storage::disk('public')->put($fileName, $htmlContent);

        return OfferLetter::updateOrCreate(
            ['job_application_id' => $application->id],
            [
                'salary' => $data['salary'],
                'benefits' => $data['benefits'],
                'reporting_date' => $data['reporting_date'],
                'employment_terms' => $data['employment_terms'],
                'pdf_path' => $fileName,
                'status' => 'Sent',
            ]
        );
    }

    public function signOfferLetter(OfferLetter $offerLetter, string $signatureData): bool
    {
        // Save digital signature
        $sigFileName = 'signatures/sig_offer_' . $offerLetter->id . '_' . time() . '.png';
        
        if (Str::startsWith($signatureData, 'data:image')) {
            $image = explode(',', $signatureData)[1];
            $decodedImage = base64_decode($image);
            Storage::disk('public')->put($sigFileName, $decodedImage);
        } else {
            return false;
        }

        $offerLetter->update([
            'digital_signature_path' => $sigFileName,
            'status' => 'Accepted',
        ]);

        // Re-generate offer letter with the signature embedded
        $application = $offerLetter->jobApplication;
        $vacancy = $application->vacancy;
        $designationName = $vacancy->designation->name ?? 'N/A';
        $positionName = $vacancy->position->name ?? 'N/A';

        $htmlContent = view('offer-letters.letter', [
            'date' => date('d M Y'),
            'applicationNumber' => $application->application_number,
            'recipientName' => $application->full_name,
            'recipientEmail' => $application->email,
            'positionName' => $positionName,
            'designationName' => $designationName,
            'salary' => $offerLetter->salary,
            'benefits' => $offerLetter->benefits,
            'reportingDate' => date('d M Y', strtotime($offerLetter->reporting_date)),
            'employmentTerms' => $offerLetter->employment_terms,
            'sigFileName' => $sigFileName,
            'signedDate' => date('d M Y'),
        ])->render();

        Storage::disk('public')->put($offerLetter->pdf_path, $htmlContent);

        return true;
    }
}
