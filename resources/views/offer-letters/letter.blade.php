@extends('offer-letters.layout')

@section('title', 'Official Employment Offer Letter')

@section('content')
    <div class="header">
        <div class="logo">SUPA / OUT UNIVERSITY</div>
        <div class="title">OFFICIAL EMPLOYMENT OFFER LETTER</div>
    </div>
    
    <div class="meta">
        <strong>Date:</strong> {{ $date }}<br>
        <strong>Ref Number:</strong> OFF/{{ $applicationNumber }}<br>
        <strong>To:</strong> {{ $recipientName }}<br>
        <strong>Email:</strong> {{ $recipientEmail }}
    </div>

    <div class="salutation">Dear {{ $recipientName }},</div>

    <p>We are pleased to offer you employment at SUPA / OUT University. Following your successful interviews and evaluations, we are excited to invite you to join our team.</p>

    <table class="terms-table">
        <tr>
            <td class="label">Position</td>
            <td>{{ $positionName }}</td>
        </tr>
        <tr>
            <td class="label">Designation</td>
            <td>{{ $designationName }}</td>
        </tr>
        <tr>
            <td class="label">Salary</td>
            <td>{{ $salary }}</td>
        </tr>
        <tr>
            <td class="label">Benefits</td>
            <td>{!! nl2br(e($benefits)) !!}</td>
        </tr>
        <tr>
            <td class="label">Reporting Date</td>
            <td>{{ $reportingDate }}</td>
        </tr>
        <tr>
            <td class="label">Employment Terms</td>
            <td>{!! nl2br(e($employmentTerms)) !!}</td>
        </tr>
    </table>

    <p>Please review the terms above. To accept this offer, please sign digitally on your portal on or before the reporting date.</p>

    <p>Congratulations, and we look forward to working with you.</p>

    <div class="signature-section">
        <div class="signature-box">
            <strong>Authorized HR Director</strong><br>
            SUPA University HR Department
        </div>
        <div class="signature-box" id="candidate-sig">
            @if(!empty($sigFileName))
                <img src="{{ asset('storage/' . $sigFileName) }}" class="sig-img" alt="Candidate Digital Signature"><br>
                <strong>Signed by {{ $recipientName }}</strong><br>
                Date: {{ $signedDate }}
            @else
                <strong>Candidate Signature</strong><br>
                <span id="sig-placeholder">Pending Acceptance</span>
            @endif
        </div>
    </div>
@endsection
