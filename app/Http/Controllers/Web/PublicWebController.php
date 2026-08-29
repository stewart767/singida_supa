<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Event;
use App\Models\Faq;
use App\Models\News;
use App\Models\Programme;
use Illuminate\Http\Request;

class PublicWebController extends Controller
{
    public function home()
    {
        $programmes = Programme::where('is_active', true)->get();
        $news = News::where('is_featured', true)->latest()->take(3)->get();
        $events = Event::where('is_active', true)->orderBy('event_date')->take(3)->get();
        $faqs = Faq::orderBy('order')->take(5)->get();

        return view('public.index', compact('programmes', 'news', 'events', 'faqs'));
    }

    public function programmes()
    {
        $programmes = Programme::where('is_active', true)->get();
        return view('public.programmes', compact('programmes'));
    }

    public function admissionRequirements()
    {
        $programmes = Programme::where('is_active', true)->get();
        return view('public.admission-requirements', compact('programmes'));
    }

    public function trackApplication()
    {
        return view('public.track-application');
    }

    public function news(Request $request)
    {
        if (!\App\Models\Setting::get('show_news_announcements', true)) {
            abort(404);
        }

        $search = $request->input('search');
        $query = News::latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('summary', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $newsList = $query->paginate(9)->withQueryString();
        return view('public.news', compact('newsList', 'search'));
    }

    public function newsShow(News $news)
    {
        if (!\App\Models\Setting::get('show_news_announcements', true)) {
            abort(404);
        }

        $recentNews = News::where('id', '!=', $news->id)->latest()->take(3)->get();
        return view('public.news-detail', compact('news', 'recentNews'));
    }

    public function events()
    {
        $eventsList = Event::where('is_active', true)->orderBy('event_date')->paginate(9);
        return view('public.events', compact('eventsList'));
    }

    public function faqs()
    {
        $faqs = Faq::orderBy('order')->get();
        return view('public.faq', compact('faqs'));
    }

    public function downloads()
    {
        $downloads = Download::all();
        return view('public.downloads', compact('downloads'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function login(Request $request)
    {
        if ($request->query('flow') !== 'career') {
            session()->forget(['selected_job_id', 'selected_job_number']);
        }
        return view('auth.login');
    }

    public function register(Request $request)
    {
        if ($request->query('flow') !== 'career') {
            session()->forget(['selected_job_id', 'selected_job_number']);
        }
        if (!\App\Models\Setting::get('applicant_login_required', false)) {
            return redirect()->route('applicant.wizard');
        }
        return view('auth.register');
    }

    public function studentGuide()
    {
        return view('pdf.student-guide-part2');
    }

    public function admissionStepsGuide()
    {
        return view('pdf.admission-steps-guide');
    }

    public function paymentGuideline()
    {
        return view('pdf.payment-guideline');
    }

    public function downloadAdmissionExcel()
    {
        $filename = 'FOMU_YA_MAOMBI_YA_UDAHILI_REQUIREMENTS_SUPA.csv';

        $rows = [
            ['CHUO KIKUU HURIA CHA TANZANIA (OUT) & CHUO CHA UALIMU SINGIDA (STTC)'],
            ['FOMU YA MAOMBI YA UDAHILI — ORODHA YA VIPENGELE NA NYARAKA ZINAZOHITAJIKA (STUDENT ADMISSION FORM REQUIREMENTS TEMPLATE)'],
            ['Ada ya Fomu: TZS 20,000/= | Tovuti Rasmi: https://supa.ac.tz | Mfumo wa Udahili: SUPA Joint Admission Portal'],
            [],
            ['HATUA (STEP)', 'NAMBA YA KIPENGELE (FIELD NO)', 'JINA LA KIPENGELE (FIELD NAME)', 'MAELEZO YA KIPENGELE (DESCRIPTION)', 'HALI (STATUS)', 'AINA YA DATA (DATA TYPE)', 'MFANO WA KUJAZA (EXAMPLE VALUE)'],
            ['Hatua ya 1: Account Verification', '1.01', 'Ridhi ya Faragha (Consent Given)', 'Kukubali sera ya ulinzi wa taarifa binafsi na masharti ya udahili', 'LAZIMA (Mandatory)', 'Chaguo (Ndiyo/Hapana)', 'Ndiyo'],
            ['Hatua ya 1: Account Verification', '1.02', 'Barua Pepe (Email Address)', 'Barua pepe hai ya mwombaji kwa ajili ya kupokea mrejesho', 'LAZIMA (Mandatory)', 'Email', 'mwombaji@example.com'],
            ['Hatua ya 1: Account Verification', '1.03', 'Namba ya Simu / WhatsApp', 'Namba ya simu inayopatikana WhatsApp kwa mawasiliano ya haraka', 'LAZIMA (Mandatory)', 'Namba ya Simu', '+255 712 345 678'],
            ['Hatua ya 1: Account Verification', '1.04', 'Nenosiri (Password)', 'Nenosiri la akaunti ya mwanafunzi (angalau herufi 8)', 'LAZIMA (Mandatory)', 'Text', 'Nenosiri@2026'],
            ['Hatua ya 2: Personal Information', '2.01', 'Jina la Kwanza (First Name)', 'Jina la kwanza kama lilivyo kwenye vyeti', 'LAZIMA (Mandatory)', 'Text', 'Juma'],
            ['Hatua ya 2: Personal Information', '2.02', 'Jina la Kati (Middle Name)', 'Jina la kati kama lipo kwenye vyeti', 'HIARI (Optional)', 'Text', 'Bakari'],
            ['Hatua ya 2: Personal Information', '2.03', 'Jina la Ukoo (Last Name / Surname)', 'Jina la mwisho/ukoo kama lilivyo kwenye vyeti', 'LAZIMA (Mandatory)', 'Text', 'Kibwana'],
            ['Hatua ya 2: Personal Information', '2.04', 'Jinsia (Gender)', 'Jinsia ya mwombaji (Mme au Mke)', 'LAZIMA (Mandatory)', 'Chaguo (Male/Female)', 'Male'],
            ['Hatua ya 2: Personal Information', '2.05', 'Tarehe ya Kuzaliwa (Date of Birth)', 'Tarehe ya kuzaliwa (Mwaka-Mwezi-Siku)', 'LAZIMA (Mandatory)', 'Tarehe (YYYY-MM-DD)', '1998-05-14'],
            ['Hatua ya 2: Personal Information', '2.06', 'Namba ya NIDA (NIDA Number)', 'Namba ya Kitambulisho cha Taifa (tarakimu 20)', 'LAZIMA (Kama huna tumia Kura/Kazi)', 'Tarakimu 20', '19980514123450000112'],
            ['Hatua ya 2: Personal Information', '2.07', 'Namba ya Kura (Voter ID Number)', 'Namba ya kitambulisho cha mpiga kura', 'LAZIMA (Kama huna NIDA/Kazi)', 'Text', 'T-1234-5678-901'],
            ['Hatua ya 2: Personal Information', '2.08', 'Namba ya Kazi (Work/Staff ID Number)', 'Namba ya kitambulisho cha kazi (kama ni mtumishi)', 'LAZIMA (Kama huna NIDA/Kura)', 'Text', 'EMP-2024-098'],
            ['Hatua ya 2: Personal Information', '2.09', 'Mkoa wa Makazi (Region)', 'Mkoa unapoishi kwa sasa', 'LAZIMA (Mandatory)', 'Text / Orodha', 'Singida'],
            ['Hatua ya 2: Personal Information', '2.10', 'Wilaya ya Makazi (District)', 'Wilaya unapoishi kwa sasa', 'LAZIMA (Mandatory)', 'Text / Orodha', 'Singida Mjini'],
            ['Hatua ya 2: Personal Information', '2.11', 'Kata ya Makazi (Ward)', 'Kata unapoishi kwa sasa', 'LAZIMA (Mandatory)', 'Text', 'Mitunduruni'],
            ['Hatua ya 2: Personal Information', '2.12', 'Jina la Mzazi/Mlezi (Parent/Guardian Name)', 'Jina la mzazi au mlezi (kama mwombaji yuko chini ya miaka 18)', 'Inahitajika kwa wenye umri chini ya miaka 18', 'Text', 'Amina Hamisi'],
            ['Hatua ya 2: Personal Information', '2.13', 'Simu ya Mzazi/Mlezi (Parent/Guardian Phone)', 'Namba ya simu ya mzazi au mlezi', 'Inahitajika kwa wenye umri chini ya miaka 18', 'Namba ya Simu', '+255 784 112 233'],
            ['Hatua ya 3: Academic Qualifications', '3.01', 'Aina ya Udahili (Admission Type)', 'Njia ya kuingilia udahili (Diploma au Form Six)', 'LAZIMA (Mandatory)', 'Chaguo (Diploma / Form Six)', 'Diploma'],
            ['Hatua ya 3: Academic Qualifications', '3.02', 'Namba ya Mtihani Kidato cha 4 (CSEE Index No)', 'Namba ya mtihani wa kidato cha nne', 'LAZIMA (Mandatory)', 'Text', 'S0123/0045/2016'],
            ['Hatua ya 3: Academic Qualifications', '3.03', 'Mwaka wa Kuhitimu Kidato cha 4 (CSEE Year)', 'Mwaka uliomaliza kidato cha nne', 'LAZIMA (Mandatory)', 'Mwaka (YYYY)', '2016'],
            ['Hatua ya 3: Academic Qualifications', '3.04', 'Namba ya Mtihani Kidato cha 6 (ACSEE Index No)', 'Namba ya mtihani wa kidato cha sita (kama ulihitimu)', 'LAZIMA kwa Form Six', 'Text', 'S0123/0501/2018'],
            ['Hatua ya 3: Academic Qualifications', '3.05', 'Mwaka wa Kuhitimu Kidato cha 6 (ACSEE Year)', 'Mwaka uliomaliza kidato cha sita', 'LAZIMA kwa Form Six', 'Mwaka (YYYY)', '2018'],
            ['Hatua ya 3: Academic Qualifications', '3.06', 'Alama za Kidato cha 6 (ACSEE Points)', 'Jumla ya points za ufaulu wa kidato cha sita', 'LAZIMA kwa Form Six', 'Namba', '6'],
            ['Hatua ya 3: Academic Qualifications', '3.07', 'Chuo cha Diploma (Diploma College Name)', 'Jina la chuo ulichosoma stashahada', 'LAZIMA kwa Diploma', 'Text', 'Singida Teachers Training College'],
            ['Hatua ya 3: Academic Qualifications', '3.08', 'Jina la Programu ya Diploma (Diploma Programme)', 'Jina la kozi ya stashahada uliyosomea', 'LAZIMA kwa Diploma', 'Text', 'Diploma in Secondary Education'],
            ['Hatua ya 3: Academic Qualifications', '3.09', 'Mwaka wa Kuhitimu Diploma (Graduation Year)', 'Mwaka uliomaliza stashahada', 'LAZIMA kwa Diploma', 'Mwaka (YYYY)', '2021'],
            ['Hatua ya 3: Academic Qualifications', '3.10', 'GPA ya Diploma (Diploma GPA Score)', 'Kiwango cha ufaulu wa GPA ya stashahada', 'LAZIMA kwa Diploma (3.0+ Direct, 2.0-2.9 Foundation)', 'Namba (Decimal)', '3.4'],
            ['Hatua ya 3: Academic Qualifications', '3.11', 'Namba ya NACTVET AVN (Award Verification No)', 'Namba ya uthibitisho wa tuzo ya NACTVET', 'LAZIMA kwa Diploma', 'Text', '21NT1029384'],
            ['Hatua ya 4: Programme Selection', '4.01', 'Programu Unayoomba (Selected Programme)', 'Programu ya chuo unayoomba kusoma', 'LAZIMA (Mandatory)', 'Chaguo la Programu', 'Bachelor of Arts with Education'],
            ['Hatua ya 4: Programme Selection', '4.02', 'Mwaka wa Masomo (Academic Year)', 'Mwaka wa masomo wa kuanza masomo', 'LAZIMA (Mandatory)', 'Mwaka', '2026/2027'],
            ['Hatua ya 4: Programme Selection', '4.03', 'Kundi la Udahili (Intake Session)', 'Mwezi wa kuanza masomo', 'LAZIMA (Mandatory)', 'Chaguo (March / September)', 'September Intake'],
            ['Hatua ya 5: Fee Payment', '5.01', 'Kiasi cha Ada ya Fomu (Application Fee Amount)', 'Ada rasmi ya fomu ya maombi ya chuo', 'LAZIMA (Mandatory)', 'Fedha (TZS)', 'TZS 20,000/='],
            ['Hatua ya 5: Fee Payment', '5.02', 'NMB Control Number', 'Namba maalum ya malipo itakayozalishwa kwenye mfumo', 'LAZIMA (Mandatory)', 'Namba ya Malipo', '991002026000001'],
            ['Hatua ya 5: Fee Payment', '5.03', 'Namba ya Muamala (Transaction Reference)', 'Namba ya kumbukumbu ya malipo kutoka benki au simu', 'LAZIMA (Mandatory)', 'Text', 'NMB-TRX-8930219'],
            ['Hatua ya 5: Fee Payment', '5.04', 'Faili la Risiti (Payment Receipt Slip)', 'Picha au PDF ya risiti ya malipo ya ada ya TZS 20,000', 'LAZIMA (Mandatory)', 'Faili (PDF/JPG/PNG max 5MB)', 'risiti_malipo_20000.pdf'],
            ['Hatua ya 6: Document Uploads', '6.01', 'Cheti cha Kidato cha 4 (CSEE Certificate)', 'Nakala halisi ya cheti au result slip ya kidato cha nne', 'LAZIMA (Mandatory)', 'Faili (PDF/JPG/PNG max 5MB)', 'cheti_kidato_cha_nne.pdf'],
            ['Hatua ya 6: Document Uploads', '6.02', 'Cheti cha Kidato cha 6 au Diploma', 'Nakala ya cheti cha kidato cha sita au stashahada', 'LAZIMA (Mandatory)', 'Faili (PDF/JPG/PNG max 5MB)', 'cheti_diploma.pdf'],
            ['Hatua ya 6: Document Uploads', '6.03', 'Matokeo ya Chuo (Academic Transcript)', 'Nakala ya transcript rasmi ya matokeo ya stashahada', 'LAZIMA kwa Diploma', 'Faili (PDF/JPG/PNG max 5MB)', 'transcript_matokeo.pdf'],
            ['Hatua ya 6: Document Uploads', '6.04', 'Nakala ya Kitambulisho (ID Copy)', 'Nakala ya NIDA ID, Kitambulisho cha Kura, au Kazi', 'LAZIMA (Mandatory)', 'Faili (PDF/JPG/PNG max 5MB)', 'kitambulisho_nida.pdf'],
            ['Hatua ya 6: Document Uploads', '6.05', 'Picha ya Pasipoti (Passport Size Photo)', 'Picha ndogo ya rangi yenye mandhari (background) nyeupe', 'LAZIMA (Mandatory)', 'Picha (JPG/PNG max 2MB)', 'passport_photo.jpg'],
            ['Hatua ya 7: Declaration & Final Submission', '7.01', 'Tamko la Usahihi wa Taarifa (Declaration Check)', 'Kuthibitisha kwamba taarifa zote zilizojazwa ni sahihi', 'LAZIMA (Mandatory)', 'Chaguo (Ndiyo)', 'Ndiyo'],
            ['Hatua ya 7: Declaration & Final Submission', '7.02', 'Saini ya Mwombaji (Digital Signature)', 'Saini ya kidigitali / Jina kamili kama saini', 'LAZIMA (Mandatory)', 'Text / Saini', 'Juma Bakari Kibwana'],
            ['Hatua ya 7: Declaration & Final Submission', '7.03', 'Namba ya Maombi (Application Number)', 'Namba rasmi itakayozalishwa mara baada ya kuwasilisha', 'Inazalishwa na Mfumo', 'Namba ya Maombi', 'SUPA-2026-0001'],
        ];

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens Swahili and special characters correctly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function privacyPolicy()
    {
        $content = \App\Models\Setting::get('privacy_policy_content', $this->getDefaultPrivacyPolicy());
        return view('public.privacy', compact('content'));
    }

    public function termsAndConditions()
    {
        $content = \App\Models\Setting::get('terms_conditions_content', $this->getDefaultTermsConditions());
        return view('public.terms', compact('content'));
    }

    private function getDefaultPrivacyPolicy()
    {
        return "<h2>1. Information We Collect</h2><p>We collect personal information when you register, apply for a programme, upload certificates, or pay fee amounts. This includes your name, email address, phone number, academic records, and data uploads.</p><h2>2. How We Use Your Information</h2><p>We use your data solely for academic admission evaluation, processing applications, auditing payments, and corresponding with you regarding selection status.</p><h2>3. Data Protection and Security</h2><p>We implement strict access control restrictions and data encryption protocols. Only authorized admission and finance officers have access to your personal files.</p>";
    }

    private function getDefaultTermsConditions()
    {
        return "<h2>1. Accuracy of Information</h2><p>By submitting an application, you certify that all information, certificates, grades, and references provided are authentic, accurate, and complete. Providing false credentials will result in immediate disqualification or cancellation of admission.</p><h2>2. Application Fees</h2><p>All application fees are non-refundable and must be processed using the official control numbers generated on the portal.</p><h2>3. Admissions Authority</h2><p>The University Admissions Committee reserves the final right to admit, reject, or defer any candidate based on academic eligibility standards and capacity.</p>";
    }
}
