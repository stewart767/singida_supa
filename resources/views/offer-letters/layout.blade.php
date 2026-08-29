<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Official Offer Letter')</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: #1E293B; line-height: 1.6; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #F59E0B; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 800; color: #0F172A; }
        .title { font-size: 20px; font-weight: 800; margin-top: 20px; text-transform: uppercase; color: #1E3A8A; }
        .meta { margin-bottom: 30px; font-size: 14px; }
        .salutation { font-weight: bold; margin-bottom: 20px; }
        .terms-table { width: 100%; margin-top: 20px; margin-bottom: 20px; border-collapse: collapse; }
        .terms-table td { padding: 10px; border: 1px solid #E2E8F0; }
        .terms-table td.label { font-weight: bold; background: #F8FAFC; width: 30%; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { border-top: 1px solid #94A3B8; width: 40%; text-align: center; padding-top: 10px; font-size: 14px; }
        .sig-img { max-height: 60px; display: block; margin: 10px auto; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
