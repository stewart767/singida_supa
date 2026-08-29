<x-public-layout title="Apply Online - STTC Recruitment">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
         x-data="recruitmentWizard()"
         x-init="init()">

        <!-- Wizard Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden">
            
            <!-- Progress Header -->
            <div class="bg-white text-white p-6 sm:p-8 space-y-4 border-b border-slate-200">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-extrabold uppercase text-amber-500 tracking-wider">STTC Recruitment Portal - Dawati la Maombi</span>
                    <span class="font-extrabold" x-text="'Hatua ' + step + ' ya ' + totalSteps"></span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black">Maombi ya Kazi: {{ $vacancy->job_title }}</h2>
                
                <!-- Progress Indicator Bar -->
                <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full transition-all duration-300" :style="'width: ' + ((step / totalSteps) * 100) + '%'"></div>
                </div>
            </div>

            <!-- Error Box -->
            <div x-show="errorsList.length > 0" class="p-6 bg-red-500/10 border-b border-red-500/20 text-red-500 text-xs font-bold space-y-1" x-cloak>
                <p class="font-black text-sm">Tafadhali rekebisha makosa yafuatayo ili kuendelea:</p>
                <ul class="list-disc list-inside space-y-1">
                    <template x-for="err in errorsList" :key="err">
                        <li x-text="err"></li>
                    </template>
                </ul>
            </div>

            <!-- Wizard Form -->
            <form @submit.prevent="submitFinal()" id="wizardForm" novalidate class="p-6 sm:p-8 text-xs font-semibold text-slate-700 space-y-8">
                
                <!-- STEP 1: Position Applying For (Nafasi Unayoomba) -->
                <div x-show="step === 1" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 1: Nafasi Unayoomba</h3>
                    <div class="p-6 bg-slate-50 rounded-2xl border space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-slate-500 block">Nafasi Inayoombwa:</span>
                                <span class="text-sm font-extrabold text-slate-900">{{ $vacancy->job_title }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Namba ya Rejea ya Kazi:</span>
                                <span class="text-sm font-extrabold text-slate-900">{{ $vacancy->vacancy_number }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Wadhifa (Designation):</span>
                                <span class="text-sm font-extrabold text-slate-900">{{ $vacancy->designation->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Kampasi (Campus):</span>
                                <span class="text-sm font-extrabold text-slate-900">{{ $vacancy->campus->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <p class="text-slate-500 text-[11px] pt-4 border-t">Kumbuka: Vigezo na maelezo ya nafasi hii yanasimamiwa kwa usalama na Msimamizi Mkuu (Super Admin).</p>
                    </div>
                </div>

                <!-- STEP 2: Personal Information (Taarifa Binafsi) -->
                <div x-show="step === 2" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 2: Taarifa Binafsi na Mawasiliano</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Passport Photo -->
                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-slate-500">Picha ya Passport * (Passport Photo)</label>
                            <input type="file" @change="uploadPassportPhoto($event)" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <span class="text-[10px] text-emerald-500" x-show="form.attachments.passport_photo" x-cloak>✓ Picha Imepakiwa (Uploaded)</span>
                        </div>

                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-slate-500">Jina Kamili *</label>
                            <input type="text" x-model="form.full_name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Jinsia *</label>
                            <select x-model="form.gender" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">Chagua Jinsia</option>
                                <option value="male">Mume (Male)</option>
                                <option value="female">Mke (Female)</option>
                                <option value="other">Nyingine (Other)</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Tarehe ya Kuzaliwa *</label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <select x-model="dob_day" x-ref="dobDay" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                        <option value="">Siku (Day)</option>
                                        @for ($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}">{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <select x-model="dob_month" x-ref="dobMonth" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                        <option value="">Mwezi (Month)</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <select x-model="dob_year" x-ref="dobYear" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                        <option value="">Mwaka (Year)</option>
                                        @for ($y = 1950; $y <= 2026; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Namba ya NIDA *</label>
                            <input type="text" x-model="form.nida_number" placeholder="NIDA Number" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Namba ya TIN (Hiyari)</label>
                            <input type="text" x-model="form.tin_number" placeholder="TIN Number" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Namba ya NSSF (Hiyari)</label>
                            <input type="text" x-model="form.nssf_number" placeholder="NSSF Number" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Simu ya Mkononi *</label>
                            <input type="text" x-model="form.phone" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Namba ya WhatsApp *</label>
                            <input type="text" x-model="form.whatsapp_number" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Barua Pepe (Email) *</label>
                            <input type="email" x-model="form.email" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Mkoa *</label>
                            <select x-model="form.region" @change="form.district = ''" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 font-semibold focus:ring-2 focus:ring-amber-500">
                                <option value="">Chagua Mkoa (Select Region)</option>
                                <template x-for="r in Object.keys(tanzaniaRegions)" :key="r">
                                    <option :value="r" x-text="r"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Wilaya *</label>
                            <select x-model="form.district" required :disabled="!form.region" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 font-semibold focus:ring-2 focus:ring-amber-500 disabled:opacity-50">
                                <option value="">Chagua Wilaya (Select District)</option>
                                <template x-for="d in (tanzaniaRegions[form.region] || [])" :key="d">
                                    <option :value="d" x-text="d"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-slate-500">Anwani ya Makazi * (Physical Address)</label>
                            <textarea x-model="form.physical_address" required rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                        </div>
                        
                        @guest
                        <div class="space-y-1.5 col-span-2 pt-4 border-t">
                            <label class="block text-slate-500">Nenosiri la Akaunti * (Password kwa ajili ya kuingia baadaye na kuangalia maendeleo ya maombi)</label>
                            <input type="password" x-model="form.password" placeholder="Isipungue herufi/namba 8" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        @endguest
                    </div>
                </div>

                <!-- STEP 3: Employment Experience (Taarifa za Ajira na Uzoefu wa Kazi) -->
                <div x-show="step === 3" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 3: Taarifa za Ajira na Uzoefu wa Kazi</h3>
                    
                    <!-- A. Previous STTC Employment Question -->
                    <div class="space-y-4 p-5 rounded-2xl bg-amber-500/5 border border-amber-500/10">
                        <span class="font-bold text-slate-900">Je, umewahi kufanya kazi Singida Teachers' Training College? (Have you worked at Singida TTC?)</span>
                        <div class="flex items-center gap-6">
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer font-bold">
                                    <input type="radio" value="1" x-model="form.worked_at_sttc"> Ndiyo (Yes)
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-bold">
                                    <input type="radio" value="0" x-model="form.worked_at_sttc"> Hapana (No)
                                </label>
                            </div>
                        </div>

                        <!-- Yes Flow: STTC Experience -->
                        <div x-show="form.worked_at_sttc == '1'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-200" x-cloak>
                            <div class="space-y-1.5">
                                <label class="block text-slate-700 font-bold">Chagua Kampasi * (Choose Campus)</label>
                                <select x-model="form.sttc_experience.campus" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 font-semibold focus:ring-2 focus:ring-amber-500">
                                    <option value="">Chagua Kampasi</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->name }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-slate-700 font-bold">Cheo * (Designation / Position held)</label>
                                <input type="text" x-model="form.sttc_experience.position_held" placeholder="e.g. Lecturer, Assistant Lecturer" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 font-semibold focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>

                        <!-- No Flow: Other Experience -->
                        <div x-show="form.worked_at_sttc == '0'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-200" x-cloak>
                            <div class="space-y-1.5">
                                <label class="block text-slate-700 font-bold">Ulikofanya Kazi * (Where did you work / Employer)</label>
                                <input type="text" x-model="form.other_experience.employer" placeholder="e.g. Ministry of Education, OUT, or Private Company" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 font-semibold focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-slate-700 font-bold">Cheo * (Designation / Position)</label>
                                <input type="text" x-model="form.other_experience.position" placeholder="e.g. Teacher, Instructor" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 font-semibold focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Education Details (Taarifa za Elimu) -->
                <div x-show="step === 4" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 4: Taarifa za Elimu</h3>
                    
                    <template x-for="(edu, index) in form.education_history" :key="index">
                        <div class="p-5 rounded-2xl border space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-amber-500" x-text="'Elimu #' + (index + 1)"></span>
                                <button type="button" @click="removeEducation(index)" class="text-red-500 font-bold hover:underline">Ondoa (Remove)</button>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Taasisi * (Institution)</label>
                                    <input type="text" x-model="edu.institution" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Kiwango cha Elimu * (Education Level)</label>
                                    <select x-model="edu.level" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                        <option value="">Chagua Kiwango</option>
                                        <option value="Primary">Primary</option>
                                        <option value="Secondary">Secondary</option>
                                        <option value="Certificate">Certificate</option>
                                        <option value="Diploma">Diploma</option>
                                        <option value="Bachelor">Bachelor Degree</option>
                                        <option value="Postgraduate Diploma">Postgraduate Diploma</option>
                                        <option value="Master's">Master's Degree</option>
                                        <option value="PhD">PhD</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Kozi / Utaalamu * (Course / Specialization)</label>
                                    <input type="text" x-model="edu.programme" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Jina la Cheti * (Certificate Award Name)</label>
                                    <input type="text" x-model="edu.award" required placeholder="E.g. Degree of Bachelor of Science" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">GPA / Division / Grade *</label>
                                    <input type="text" x-model="edu.gpa_grade" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Mwaka wa Kuanza *</label>
                                    <input type="number" x-model="edu.start_year" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Mwaka wa Kumaliza *</label>
                                    <input type="number" x-model="edu.completion_year" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Pakia Cheti * (Upload Cheti)</label>
                                    <input type="file" @change="uploadEduCertificate($event, index)" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                    <span class="text-[10px] text-emerald-500" x-show="edu.certificate_path" x-cloak>✓ Cheti Kimepakiwa</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="addEducation()" class="w-full py-3 rounded-2xl border border-dashed hover:bg-slate-50 text-center font-bold text-slate-500">
                        + Ongeza Elimu Nyingine
                    </button>
                </div>

                <!-- STEP 5: ICT Competency (Matumizi ya TEHAMA) -->
                <div x-show="step === 5" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 5: Matumizi ya TEHAMA katika kazi</h3>
                    
                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Elezea uwezo wako wa kutumia TEHAMA katika kazi *</label>
                        <textarea x-model="form.ict_description" required rows="4" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>

                    <div class="space-y-3 pt-4">
                        <span class="block text-slate-500 font-bold">Ujuzi wa Kompyuta (Computer Skills):</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="skillName in predefinedIctSkills" :key="skillName">
                                <div class="p-3 rounded-xl border flex items-center justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" :value="skillName" :checked="hasIctSkill(skillName)" @change="toggleIctSkill(skillName)">
                                        <span x-text="skillName"></span>
                                    </label>
                                    
                                    <div x-show="hasIctSkill(skillName)" x-cloak>
                                        <select @change="updateIctSkillLevel(skillName, $event.target.value)" :value="getIctSkillLevel(skillName)" class="p-1.5 bg-white border rounded-lg text-[10px] text-slate-955">
                                            <option value="Beginner">Beginner</option>
                                            <option value="Intermediate">Intermediate</option>
                                            <option value="Advanced">Advanced</option>
                                            <option value="Expert">Expert</option>
                                        </select>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- STEP 6: Teaching Experience & Professional Qualifications (Uzoefu wa Kufundisha / Sifa za Kitaaluma) -->
                <div x-show="step === 6" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 6: Sifa za Kitaaluma & Uzoefu wa Kazi</h3>
                    
                    <!-- If Teacher type: Teaching Experience Section -->
                    <template x-if="positionType === 'teacher'">
                        <div class="space-y-6 bg-slate-50 p-6 rounded-2xl border">
                            <h4 class="font-extrabold text-xs text-amber-500 uppercase tracking-wider pb-2 border-b">Uzoefu wa Kufundisha</h4>
                            
                            <div class="space-y-2">
                                <label class="block text-slate-500">Umebobea kufundisha masomo gani? * (Chagua masomo husika)</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <template x-for="subj in predefinedSubjects" :key="subj">
                                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg border bg-white">
                                            <input type="checkbox" :value="subj" :checked="form.teaching_subjects.includes(subj)" @change="toggleSubject(subj)">
                                            <span x-text="subj"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-slate-500">Masomo Mengine (Kama hayapo kwenye orodha hapo juu)</label>
                                <input type="text" x-model="form.teaching_other_subjects" placeholder="E.g. Kiswahili cha Juu, nk" class="w-full p-3 bg-white rounded-xl border border-slate-200 text-slate-950">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Miaka ya Uzoefu wa Kufundisha *</label>
                                    <input type="number" x-model="form.teaching_years" min="0" placeholder="E.g. 3" class="w-full p-3 bg-white rounded-xl border border-slate-200 text-slate-950">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-500">Ngazi Uliyofundisha *</label>
                                    <select x-model="form.teaching_level" class="w-full p-3 bg-white rounded-xl border border-slate-200 text-slate-950">
                                        <option value="">Chagua Ngazi</option>
                                        <option value="Primary">Primary (Msingi)</option>
                                        <option value="Secondary">Secondary (Sekondari)</option>
                                        <option value="College">College (Chuo)</option>
                                        <option value="University">University (Chuo Kikuu)</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5 col-span-2">
                                    <label class="block text-slate-500">Taasisi Uliyofundisha *</label>
                                    <input type="text" x-model="form.teaching_institution" placeholder="Jina la shule au chuo ulichofundisha" class="w-full p-3 bg-white rounded-xl border border-slate-200 text-slate-950">
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Professional Qualifications checklist dynamic rendering -->
                    <div class="space-y-4">
                        <span class="block text-slate-500 font-bold">Veti vya Taaluma / Bodi za Kitaalamu (Professional Qualifications):</span>
                        <p class="text-slate-500 text-[11px]">Chagua vyeti au bodi za kitaaluma ambazo umesajiliwa nazo kulingana na nafasi hii.</p>

                        <!-- Qualifications builder list -->
                        <div class="space-y-4">
                            <template x-for="qual in qualificationsList" :key="qual">
                                <div class="p-4 rounded-xl border space-y-4 bg-slate-50/50">
                                    <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-900">
                                        <input type="checkbox" :value="qual" :checked="hasQualification(qual)" @change="toggleQualification(qual)">
                                        <span x-text="qual"></span>
                                    </label>

                                    <!-- Expanded details inside qualification -->
                                    <div x-show="hasQualification(qual)" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pl-6 pt-3 border-t" x-cloak>
                                        <div class="space-y-1">
                                            <label class="block text-slate-500">Registration Number *</label>
                                            <input type="text" :value="getQualData(qual).registration_number" @input="updateQualField(qual, 'registration_number', $event.target.value)" placeholder="Namba ya Usajili" class="w-full p-2 bg-white rounded-lg border">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="block text-slate-500">Date Issued *</label>
                                            <input type="date" :value="getQualData(qual).date_issued" @input="updateQualField(qual, 'date_issued', $event.target.value)" class="w-full p-2 bg-white rounded-lg border">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="block text-slate-500">Expiry Date (Kama ipo)</label>
                                            <input type="date" :value="getQualData(qual).expiry_date" @input="updateQualField(qual, 'expiry_date', $event.target.value)" class="w-full p-2 bg-white rounded-lg border">
                                        </div>
                                        <div class="space-y-1">
                                            <label class="block text-slate-500">Pakia Cheti * (Certificate Upload)</label>
                                            <input type="file" @change="uploadQualificationFile($event, qual)" class="w-full p-1 bg-white rounded-lg border text-[10px]">
                                            <span class="text-[10px] text-emerald-500 block mt-1" x-show="getQualData(qual).certificate_path" x-cloak>✓ Cheti Kimepakiwa</span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- If "other" or custom options are needed -->
                            <div class="space-y-4">
                                <span class="block text-slate-500 font-bold">Veti au Vyeti Vingine vya Kitaalamu:</span>
                                <template x-for="(cq, cIdx) in customQualifications" :key="cIdx">
                                    <div class="p-4 rounded-xl border bg-slate-50/50 space-y-4">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-amber-500">Sifa ya Nyongeza #<span x-text="cIdx + 1"></span></span>
                                            <button type="button" @click="removeCustomQual(cIdx)" class="text-red-500 hover:underline">Ondoa</button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-1 col-span-2">
                                                <label class="block text-slate-500">Jina la Cheti / Bodi *</label>
                                                <input type="text" x-model="cq.name" required placeholder="E.g. CCNA" class="w-full p-2 bg-white rounded-lg border">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-slate-500">Registration Number *</label>
                                                <input type="text" x-model="cq.registration_number" required placeholder="Namba ya Usajili" class="w-full p-2 bg-white rounded-lg border">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-slate-500">Date Issued *</label>
                                                <input type="date" x-model="cq.date_issued" required class="w-full p-2 bg-white rounded-lg border">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-slate-500">Expiry Date (Kama ipo)</label>
                                                <input type="date" x-model="cq.expiry_date" class="w-full p-2 bg-white rounded-lg border">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-slate-500">Pakia Cheti * (Certificate Upload)</label>
                                                <input type="file" @change="uploadCustomQualFile($event, cIdx)" class="w-full p-1 bg-white rounded-lg border text-[10px]">
                                                <span class="text-[10px] text-emerald-500 block mt-1" x-show="cq.certificate_path" x-cloak>✓ Cheti Kimepakiwa</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <button type="button" @click="addCustomQual()" class="px-4 py-2 border border-dashed rounded-xl hover:bg-slate-50 text-slate-500 font-bold">
                                    + Ongeza Sifa Nyingine ya Kitaaluma
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                                <!-- STEP 7: Motivation Letter (Barua ya Maombi / Ushawishi) -->
                <div x-show="step === 7" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 7: Barua ya Maombi / Ushawishi</h3>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Eleza kwa nini unafaa kwa nafasi hii na mchango utakaoleta kwa Singida Teachers' Training College *</label>
                        <textarea x-model="form.motivation_letter" required rows="8" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                </div>

                <!-- STEP 8: Attachments (Viamatisho) -->
                <div x-show="step === 8" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 8: Pakia Viamatisho</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Required Attachments -->
                        <template x-for="docKey in mandatoryDocs" :key="docKey">
                            <div class="space-y-1.5 p-4 rounded-xl border bg-slate-50/50">
                                <label class="block text-slate-500 font-bold uppercase" x-text="docKey.replace('_', ' ') + ' *'"></label>
                                <input type="file" @change="uploadAttachment($event, docKey)" class="w-full p-2 bg-white border rounded-lg">
                                <span class="text-[10px] text-emerald-500 block mt-1" x-show="form.attachments[docKey]" x-cloak>✓ Kiamatisho Kimepakiwa</span>
                            </div>
                        </template>

                        <!-- Optional Attachments -->
                        <template x-for="docKey in optionalDocs" :key="docKey">
                            <div class="space-y-1.5 p-4 rounded-xl border bg-slate-50/50">
                                <label class="block text-slate-500 font-bold uppercase" x-text="docKey.replace('_', ' ') + ' (Hiyari)'"></label>
                                <input type="file" @change="uploadAttachment($event, docKey)" class="w-full p-2 bg-white border rounded-lg">
                                <span class="text-[10px] text-emerald-500 block mt-1" x-show="form.attachments[docKey]" x-cloak>✓ Kiamatisho Kimepakiwa</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- STEP 9: Declaration & Submit (Uthibitisho na Kuwasilisha) -->
                <div x-show="step === 9" x-cloak class="space-y-6">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Hatua ya 9: Uthibitisho na Kuwasilisha</h3>
                    
                    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-xs font-bold text-emerald-600 space-y-2">
                        <p class="font-black text-sm">Uhakiki wa Maombi yako:</p>
                        <p>Hakiki taarifa zako hapa chini kabla ya kutuma. Unaweza kupakua muhtasari wa maombi yako kwa kubonyeza kitufe kilicho chini.</p>
                        <div class="pt-2">
                            <a :href="getUrl('/careers/apply/preview/' + applicationId)" target="_blank" class="px-4 py-2 bg-slate-800 text-white rounded-xl hover:bg-slate-700 inline-block">
                                Pakua Muhtasari (PDF Preview)
                            </a>
                        </div>
                    </div>

                    <div class="p-5 border rounded-2xl space-y-3 font-semibold text-slate-500">
                        <div>Jina Kamili: <span class="text-slate-800 font-bold" x-text="form.full_name"></span></div>
                        <div>Barua Pepe: <span class="text-slate-800 font-bold" x-text="form.email"></span></div>
                        <div>Simu: <span class="text-slate-800 font-bold" x-text="form.phone"></span></div>
                        <div>Namba ya NIDA: <span class="text-slate-800 font-bold" x-text="form.nida_number"></span></div>
                    </div>

                    <div class="space-y-4 pt-4 border-t">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" x-model="form.certified_correct" required class="mt-1">
                            <span class="text-slate-600 font-semibold leading-relaxed">
                                Nathibitisha kuwa taarifa zilizotolewa katika maombi haya ni za kweli na sahihi. Nakubali kuwa taarifa yoyote ya uongo au nyaraka feki ikibainika itasababisha kufutwa kwa maombi haya au kusitishwa kwa mkataba wa kazi. *
                            </span>
                        </label>

                        <!-- Signature Canvas -->
                        <div class="space-y-3 pt-4">
                            <label class="block text-slate-500 font-bold">Weka Saini ya Kidijitali * (Draw Digital Signature)</label>
                            <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50">
                                <canvas id="signatureCanvas" class="w-full h-40 cursor-crosshair block bg-white"></canvas>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="clearSignature()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200">Futa (Clear)</button>
                            </div>
                        </div>

                        <div class="space-y-1.5 pt-4">
                            <span class="text-slate-500 block font-bold">Tarehe ya Sasa:</span>
                            <span class="text-sm font-extrabold text-slate-900">{{ date('d F Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Wizard Navigation Actions -->
                <div class="flex justify-between items-center pt-6 border-t">
                    <button type="button" x-show="step > 1" x-cloak @click="prevStep()" class="px-5 py-3 rounded-2xl border font-extrabold hover:bg-slate-50 transition-colors">
                        &larr; Nyuma (Back)
                    </button>
                    <div x-show="step === 1" x-cloak></div> <!-- alignment spacer -->

                    <button type="button" x-show="step < totalSteps" x-cloak @click="saveAndContinue()" class="gradient-btn px-6 py-3 rounded-2xl text-white font-extrabold shadow-md">
                        Endelea (Continue) &rarr;
                    </button>

                    <button type="submit" x-show="step === totalSteps" x-cloak class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold shadow-xl">
                        Wasilisha Maombi (Submit)
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Wizard Javascript Logic -->
    <script>
        const tanzaniaRegions = {
            "Arusha": ["Arusha Mjini", "Arusha Vijijini", "Meru", "Karatu", "Monduli", "Longido", "Ngorongoro", "Arusha Urban"],
            "Dar es Salaam": ["Ilala", "Kinondoni", "Temeke", "Kigamboni", "Ubungo"],
            "Dodoma": ["Dodoma Mjini", "Bahi", "Chamwino", "Chemba", "Kondoa", "Mpwapwa", "Kongwa", "Dodoma"],
            "Geita": ["Geita Mjini", "Geita Vijijini", "Chato", "Bukombe", "Mbogwe", "Nyang'hwale"],
            "Iringa": ["Iringa Mjini", "Iringa Vijijini", "Kilolo", "Mufindi"],
            "Kagera": ["Bukoba Mjini", "Bukoba Vijijini", "Muleba", "Karagwe", "Biharamulo", "Ngara", "Kyerwa", "Missenyi"],
            "Katavi": ["Mpanda Mjini", "Mpanda Vijijini", "Nsimbo", "Mlele", "Tanganyika"],
            "Kigoma": ["Kigoma Ujiji", "Kigoma Vijijini", "Kasulu Mjini", "Kasulu Vijijini", "Kibondo", "Kakonko", "Uvinza", "Buhigwe"],
            "Kilimanjaro": ["Moshi Mjini", "Moshi Vijijini", "Hai", "Rombo", "Mwanga", "Same", "Siha"],
            "Lindi": ["Lindi Mjini", "Lindi Vijijini", "Kilwa", "Ruangwa", "Nachingwea", "Liwale"],
            "Manyara": ["Babati Mjini", "Babati Vijijini", "Hanang", "Mbulu", "Simanjiro", "Kiteto"],
            "Mara": ["Musoma Mjini", "Musoma Vijijini", "Tarime", "Serengeti", "Bunda", "Rorya", "Butiama"],
            "Mbeya": ["Mbeya Mjini", "Mbeya Vijijini", "Rungwe", "Kyela", "Chunya", "Mbarali", "Busokelo"],
            "Morogoro": ["Morogoro Mjini", "Morogoro Vijijini", "Kilosa", "Kilombero", "Ulanga", "Gairo", "Mvomero", "Malinyi"],
            "Mtwara": ["Mtwara Mjini", "Mtwara Vijijini", "Masasi", "Masasi Mjini", "Nanyumbu", "Newala", "Tandahimba"],
            "Mwanza": ["Nyamagana", "Ilemela", "Sengerema", "Misungwi", "Magu", "Ukerewe", "Kwimba"],
            "Njombe": ["Njombe Mjini", "Njombe Vijijini", "Wanging'ombe", "Makete", "Ludewa"],
            "Pemba North": ["Wete", "Micheweni"],
            "Pemba South": ["Chake Chake", "Mkoani"],
            "Pwani": ["Bagamoyo", "Chalinze", "Kibaha Mjini", "Kibaha Vijijini", "Kisarawe", "Mkuranga", "Rufiji", "Mafia", "Kibiti"],
            "Rukwa": ["Sumbawanga Mjini", "Sumbawanga Vijijini", "Kalambo", "Nkasi"],
            "Ruvuma": ["Songea Mjini", "Songea Vijijini", "Mbinga", "Mbinga Mjini", "Nyasa", "Tunduru", "Namtumbo"],
            "Shinyanga": ["Shinyanga Mjini", "Shinyanga Vijijini", "Kahama Mjini", "Ushetu", "Msalala", "Kishapu"],
            "Simiyu": ["Bariadi Mjini", "Bariadi Vijijini", "Maswa", "Meatu", "Itilima", "Busega"],
            "Singida": ["Singida Mjini", "Singida Vijijini", "Iramba", "Manyoni", "Ikungi", "Mkalama", "Itigi", "Singida Manispaa", "Singida"],
            "Songwe": ["Mbozi", "Momba", "Tunduma", "Ileje", "Songwe"],
            "Tabora": ["Tabora Mjini", "Uyui", "Nzega", "Nzega Mjini", "Igunga", "Sikonge", "Urambo", "Kaliua"],
            "Tanga": ["Tanga Mjini", "Muheza", "Korogwe", "Korogwe Vijijini", "Lushoto", "Mkinga", "Pangani", "Handeni", "Handeni Vijijini", "Bumbuli", "Kilindi"],
            "Zanzibar North": ["Kaskazini A", "Kaskazini B"],
            "Zanzibar South": ["Kusini", "Kati"],
            "Zanzibar Urban/West": ["Mjini", "Magharibi A", "Magharibi B"]
        };

        function recruitmentWizard() {
            return {
                step: 1,
                totalSteps: 9,
                applicationId: null,
                vacancyId: {!! json_encode($vacancy->id) !!},
                positionType: {!! json_encode($positionType) !!},
                errorsList: [],
                getUrl(path) {
                    const current = window.location.pathname;
                    const segments = current.split('/');
                    const careersIdx = segments.indexOf('careers');
                    if (careersIdx !== -1) {
                        const baseSegments = segments.slice(0, careersIdx);
                        const basePath = baseSegments.join('/');
                        return basePath + path;
                    }
                    return path;
                },
                
                // Predefined lists
                predefinedIctSkills: ['Microsoft Word', 'Microsoft Excel', 'Microsoft PowerPoint', 'Outlook', 'Internet', 'Google Workspace', 'LMS', 'ERP', 'AI Tools', 'Programming', 'Graphic Design', 'Other'],
                predefinedSubjects: ['Mathematics', 'English', 'Kiswahili', 'Physics', 'Chemistry', 'Biology', 'Geography', 'History', 'Civics', 'Bookkeeping', 'Commerce', 'ICT', 'Agriculture'],
                
                mandatoryDocs: ['cv', 'cover_letter', 'academic_certificates', 'academic_transcripts', 'nida', 'passport_photo'],
                optionalDocs: ['nssf', 'tin', 'professional_membership', 'recommendation_letter', 'training_certificates'],

                dob_day: '',
                dob_month: '',
                dob_year: '',

                form: {
                    full_name: '',
                    gender: '',
                    date_of_birth: '',
                    nida_number: '',
                    tin_number: '',
                    nssf_number: '',
                    phone: '',
                    whatsapp_number: '',
                    email: '',
                    region: '',
                    district: '',
                    physical_address: '',
                    password: '', // for guest registration

                    // Experience
                    worked_at_sttc: '0',
                    sttc_experience: {
                        campus: '',
                        department: '',
                        position_held: '',
                        start_year: '',
                        end_year: '',
                        reason_for_leaving: ''
                    },
                    other_experience: {
                        employer: '',
                        position: ''
                    },
                    experience_history: [],

                    // Education
                    education_history: [],

                    // ICT Competency
                    ict_description: '',
                    ict_skills: [],

                    // Teaching Experience details (academic-only)
                    teaching_subjects: [],
                    teaching_other_subjects: '',
                    teaching_years: '',
                    teaching_level: '',
                    teaching_institution: '',

                    // Qualifications
                    professional_qualifications: [],

                    // Referees
                    referees: [],

                    // Motivation Letter
                    motivation_letter: '',

                    // Attachments upload paths mapping
                    attachments: {},

                    // Declaration
                    certified_correct: false,
                    digital_signature: ''
                },

                customQualifications: [],

                init() {
                    this.initDobFields();
                    this.$watch('dob_day', () => this.updateDobString());
                    this.$watch('dob_month', () => this.updateDobString());
                    this.$watch('dob_year', () => this.updateDobString());

                    // Populate with draft values if available
                    @if(isset($draft))
                        const draft = {!! json_encode($draft) !!};
                        this.applicationId = draft.id;
                        this.step = draft.current_step || 1;
                        
                        this.form.full_name = draft.full_name || '';
                        this.form.gender = draft.gender || '';
                        this.form.date_of_birth = draft.date_of_birth ? draft.date_of_birth.substring(0,10) : '';
                        this.initDobFields();
                        this.form.nida_number = draft.nida_number || '';
                        this.form.tin_number = draft.tin_number || '';
                        this.form.nssf_number = draft.nssf_number || '';
                        this.form.phone = draft.phone || '';
                        this.form.whatsapp_number = draft.whatsapp_number || '';
                        this.form.email = draft.email || '';
                        this.form.region = draft.region || '';
                        this.form.district = draft.district || '';
                        this.form.physical_address = draft.physical_address || '';
                        
                        this.form.worked_at_sttc = draft.worked_at_sttc ? '1' : '0';
                        this.form.sttc_experience = draft.sttc_experience || { campus: '', department: '', position_held: '', start_year: '', end_year: '', reason_for_leaving: '' };
                        this.form.other_experience = (draft.experience_history && draft.experience_history[0]) 
                            ? {
                                employer: draft.experience_history[0].employer || '',
                                position: draft.experience_history[0].position || ''
                              }
                            : { employer: '', position: '' };
                        this.form.experience_history = draft.experience_history || [];
                        this.form.education_history = draft.education_history || [];
                        this.form.ict_description = draft.ict_description || '';
                        this.form.ict_skills = draft.ict_skills || [];
                        this.form.referees = draft.referees || [];
                        this.form.motivation_letter = draft.motivation_letter || '';
                        this.form.attachments = draft.attachments || {};

                        // Map qualifications and teaching experience from professional_qualifications array/object
                        if (draft.professional_qualifications) {
                            if (draft.professional_qualifications.teaching_details) {
                                const td = draft.professional_qualifications.teaching_details;
                                this.form.teaching_subjects = td.specialized_subjects || [];
                                this.form.teaching_other_subjects = td.other_subjects || '';
                                this.form.teaching_years = td.years_experience || '';
                                this.form.teaching_level = td.level_taught || '';
                                this.form.teaching_institution = td.institution_taught || '';
                            }
                            
                            const quals = draft.professional_qualifications.qualifications || [];
                            // Distinguish between predefined qualifications and custom qualifications
                            const predefinedList = this.qualificationsList;
                            this.form.professional_qualifications = quals.filter(q => predefinedList.includes(q.name));
                            this.customQualifications = quals.filter(q => !predefinedList.includes(q.name));
                        }
                    @endif

                    // Default arrays to prevent empty checks
                    if (this.form.experience_history.length === 0) {
                        this.addExperience();
                    }
                    if (this.form.education_history.length === 0) {
                        this.addEducation();
                    }


                    // Watch for step 9 to draw signature pad
                    this.$watch('step', (val) => {
                        if (val === 9) {
                            this.$nextTick(() => {
                                this.initSignaturePad();
                            });
                        }
                    });
                },

                initDobFields() {
                    if (this.form && this.form.date_of_birth) {
                        const parts = this.form.date_of_birth.split('-');
                        if (parts.length === 3) {
                            this.dob_year = String(parseInt(parts[0], 10));
                            this.dob_month = String(parseInt(parts[1], 10));
                            this.dob_day = String(parseInt(parts[2], 10));
                        }
                    }
                },

                updateDobString() {
                    const day = this.dob_day || (this.$refs && this.$refs.dobDay ? this.$refs.dobDay.value : '');
                    const month = this.dob_month || (this.$refs && this.$refs.dobMonth ? this.$refs.dobMonth.value : '');
                    const year = this.dob_year || (this.$refs && this.$refs.dobYear ? this.$refs.dobYear.value : '');

                    if (year && month && day) {
                        const pad = (num) => num.toString().padStart(2, '0');
                        this.form.date_of_birth = `${year}-${pad(month)}-${pad(day)}`;
                    } else {
                        this.form.date_of_birth = '';
                    }
                },

                prevStep() {
                    this.step--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                // Experience management
                addExperience() {
                    this.form.experience_history.push({ employer: '', position: '', start_year: '', end_year: '', responsibilities: '', employment_type: '' });
                },
                removeExperience(index) {
                    this.form.experience_history.splice(index, 1);
                },

                // Education management
                addEducation() {
                    this.form.education_history.push({ institution: '', level: '', award: '', programme: '', start_year: '', completion_year: '', gpa_grade: '', certificate_path: '' });
                },
                removeEducation(index) {
                    this.form.education_history.splice(index, 1);
                },
                uploadEduCertificate(event, index) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const formData = new FormData();
                    formData.append('step', '4');
                    formData.append('vacancy_id', this.vacancyId);
                    formData.append('application_id', this.applicationId || '');
                    formData.append('certificates[' + index + ']', file);
                    formData.append('education_history['+index+'][institution]', this.form.education_history[index].institution);
                    formData.append('education_history['+index+'][level]', this.form.education_history[index].level);
                    formData.append('education_history['+index+'][award]', this.form.education_history[index].award);
                    formData.append('education_history['+index+'][programme]', this.form.education_history[index].programme);
                    formData.append('education_history['+index+'][start_year]', this.form.education_history[index].start_year);
                    formData.append('education_history['+index+'][completion_year]', this.form.education_history[index].completion_year);
                    formData.append('education_history['+index+'][gpa_grade]', this.form.education_history[index].gpa_grade);

                    fetch(this.getUrl('/careers/apply/save-step'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        }
                        if (data.success) {
                            this.applicationId = data.application_id;
                            this.form.education_history[index].certificate_path = 'uploaded';
                        }
                    });
                },

                // ICT Competency management
                toggleIctSkill(skillName) {
                    const exists = this.form.ict_skills.find(s => s.skill === skillName);
                    if (exists) {
                        this.form.ict_skills = this.form.ict_skills.filter(s => s.skill !== skillName);
                    } else {
                        this.form.ict_skills.push({ skill: skillName, level: 'Beginner' });
                    }
                },
                hasIctSkill(skillName) {
                    return this.form.ict_skills.some(s => s.skill === skillName);
                },
                getIctSkillLevel(skillName) {
                    const skill = this.form.ict_skills.find(s => s.skill === skillName);
                    return skill ? skill.level : 'Beginner';
                },
                updateIctSkillLevel(skillName, level) {
                    const skill = this.form.ict_skills.find(s => s.skill === skillName);
                    if (skill) {
                        skill.level = level;
                    }
                },

                // Dynamic Qualifications list based on position type
                get qualificationsList() {
                    if (this.positionType === 'teacher') {
                        return ['TCU/TIE/TSC Registration', 'Teaching Certificate'];
                    } else if (this.positionType === 'accountant') {
                        return ['CPA', 'NBAA Registration', 'CISA (Optional)', 'ACCA (Optional)'];
                    } else if (this.positionType === 'procurement') {
                        return ['Procurement Board Registration', 'PSPTB Registration', 'Procurement Certificate'];
                    } else if (this.positionType === 'hr') {
                        return ['HR Registration', 'Labour Law Certificate'];
                    } else if (this.positionType === 'ict') {
                        return ['CCNA', 'Microsoft Certification', 'AWS', 'Azure'];
                    }
                    return [];
                },
                
                toggleQualification(qualName) {
                    const exists = this.form.professional_qualifications.find(q => q.name === qualName);
                    if (exists) {
                        this.form.professional_qualifications = this.form.professional_qualifications.filter(q => q.name !== qualName);
                    } else {
                        this.form.professional_qualifications.push({
                            name: qualName,
                            registration_number: '',
                            date_issued: '',
                            expiry_date: '',
                            certificate_path: ''
                        });
                    }
                },
                hasQualification(qualName) {
                    return this.form.professional_qualifications.some(q => q.name === qualName);
                },
                getQualData(qualName) {
                    return this.form.professional_qualifications.find(q => q.name === qualName) || {};
                },
                updateQualField(qualName, field, value) {
                    const qual = this.form.professional_qualifications.find(q => q.name === qualName);
                    if (qual) {
                        qual[field] = value;
                    }
                },
                
                // File upload for predefined qualifications
                uploadQualificationFile(event, qualName) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const qIdx = this.form.professional_qualifications.findIndex(q => q.name === qualName);
                    if (qIdx === -1) return;

                    const formData = new FormData();
                    formData.append('step', '6');
                    formData.append('vacancy_id', this.vacancyId);
                    formData.append('application_id', this.applicationId || '');
                    formData.append('qualification_file', file);
                    formData.append('qualification_name', qualName);

                    fetch(this.getUrl('/careers/apply/save-step'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        }
                        if (data.success) {
                            this.applicationId = data.application_id;
                            this.form.professional_qualifications[qIdx].certificate_path = data.path;
                        }
                    });
                },

                // Custom Qualifications management (General builder)
                addCustomQual() {
                    this.customQualifications.push({
                        name: '',
                        registration_number: '',
                        date_issued: '',
                        expiry_date: '',
                        certificate_path: ''
                    });
                },
                removeCustomQual(index) {
                    this.customQualifications.splice(index, 1);
                },
                uploadCustomQualFile(event, index) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('step', '6');
                    formData.append('vacancy_id', this.vacancyId);
                    formData.append('application_id', this.applicationId || '');
                    formData.append('qualification_file', file);
                    formData.append('qualification_name', 'custom_' + index);

                    fetch(this.getUrl('/careers/apply/save-step'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        }
                        if (data.success) {
                            this.applicationId = data.application_id;
                            this.customQualifications[index].certificate_path = data.path;
                        }
                    });
                },

                // Teacher subjects multi-select
                toggleSubject(subj) {
                    if (this.form.teaching_subjects.includes(subj)) {
                        this.form.teaching_subjects = this.form.teaching_subjects.filter(s => s !== subj);
                    } else {
                        this.form.teaching_subjects.push(subj);
                    }
                },



                // Attachments upload handler
                uploadAttachment(event, docKey) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('step', '8');
                    formData.append('vacancy_id', this.vacancyId);
                    formData.append('application_id', this.applicationId || '');
                    formData.append(docKey, file);

                    fetch(this.getUrl('/careers/apply/save-step'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        }
                        if (data.success) {
                            this.applicationId = data.application_id;
                            this.form.attachments[docKey] = 'uploaded';
                        }
                    });
                },

                // Passport Photo upload (in step 2)
                uploadPassportPhoto(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('step', '2');
                    formData.append('vacancy_id', this.vacancyId);
                    formData.append('application_id', this.applicationId || '');
                    formData.append('passport_photo', file);

                    fetch(this.getUrl('/careers/apply/save-step'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        }
                        if (data.success) {
                            this.applicationId = data.application_id;
                            this.form.attachments.passport_photo = 'uploaded';
                        }
                    });
                },

                // Signature Canvas
                initSignaturePad() {
                    const canvas = document.getElementById('signatureCanvas');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    canvas.width = canvas.parentElement.offsetWidth || 600;
                    canvas.height = 150;
                    ctx.strokeStyle = '#0f172a';
                    ctx.lineWidth = 3;
                    let drawing = false;

                    canvas.addEventListener('mousedown', (e) => {
                        drawing = true;
                        ctx.beginPath();
                        ctx.moveTo(e.offsetX, e.offsetY);
                    });
                    canvas.addEventListener('mousemove', (e) => {
                        if (drawing) {
                            ctx.lineTo(e.offsetX, e.offsetY);
                            ctx.stroke();
                        }
                    });
                    canvas.addEventListener('mouseup', () => drawing = false);
                    canvas.addEventListener('mouseleave', () => drawing = false);

                    // Touch support
                    canvas.addEventListener('touchstart', (e) => {
                        const rect = canvas.getBoundingClientRect();
                        drawing = true;
                        ctx.beginPath();
                        ctx.moveTo(e.touches[0].clientX - rect.left, e.touches[0].clientY - rect.top);
                    });
                    canvas.addEventListener('touchmove', (e) => {
                        if (drawing) {
                            const rect = canvas.getBoundingClientRect();
                            ctx.lineTo(e.touches[0].clientX - rect.left, e.touches[0].clientY - rect.top);
                            ctx.stroke();
                        }
                    });
                    canvas.addEventListener('touchend', () => drawing = false);
                },
                clearSignature() {
                    const canvas = document.getElementById('signatureCanvas');
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                },

                // Save Wizard Step AJAX Call
                saveAndContinue() {
                    this.updateDobString();
                    this.errorsList = [];
                    const formData = new FormData();
                    formData.append('step', this.step);
                    formData.append('vacancy_id', this.vacancyId);
                    formData.append('application_id', this.applicationId || '');

                    // Append data fields based on current step
                    if (this.step === 1) {
                        // Position Applying For (Confirmation Step) - no payload needed
                    } else if (this.step === 2) {
                        formData.append('full_name', this.form.full_name);
                        formData.append('gender', this.form.gender);
                        formData.append('date_of_birth', this.form.date_of_birth);
                        formData.append('nida_number', this.form.nida_number);
                        formData.append('tin_number', this.form.tin_number);
                        formData.append('nssf_number', this.form.nssf_number);
                        formData.append('phone', this.form.phone);
                        formData.append('whatsapp_number', this.form.whatsapp_number);
                        formData.append('email', this.form.email);
                        formData.append('region', this.form.region);
                        formData.append('district', this.form.district);
                        formData.append('physical_address', this.form.physical_address);
                        if (this.form.password) {
                            formData.append('password', this.form.password);
                        }
                    } else if (this.step === 3) {
                        formData.append('worked_at_sttc', this.form.worked_at_sttc);
                        if (this.form.worked_at_sttc == '1') {
                            formData.append('sttc_experience[campus]', this.form.sttc_experience.campus || '');
                            formData.append('sttc_experience[position_held]', this.form.sttc_experience.position_held || '');
                            formData.append('sttc_experience[department]', this.form.sttc_experience.department || 'N/A');
                            formData.append('sttc_experience[start_year]', this.form.sttc_experience.start_year || '2020');
                            formData.append('sttc_experience[end_year]', this.form.sttc_experience.end_year || '2023');
                            formData.append('sttc_experience[reason_for_leaving]', this.form.sttc_experience.reason_for_leaving || '');
                        } else {
                            formData.append('experience_history[0][employer]', this.form.other_experience.employer || '');
                            formData.append('experience_history[0][position]', this.form.other_experience.position || '');
                            formData.append('experience_history[0][start_year]', '2020');
                            formData.append('experience_history[0][end_year]', '2023');
                            formData.append('experience_history[0][responsibilities]', 'N/A');
                            formData.append('experience_history[0][employment_type]', 'Permanent');
                        }
                    } else if (this.step === 4) {
                        this.form.education_history.forEach((edu, idx) => {
                            formData.append(`education_history[${idx}][institution]`, edu.institution);
                            formData.append(`education_history[${idx}][level]`, edu.level);
                            formData.append(`education_history[${idx}][award]`, edu.award);
                            formData.append(`education_history[${idx}][programme]`, edu.programme);
                            formData.append(`education_history[${idx}][start_year]`, edu.start_year);
                            formData.append(`education_history[${idx}][completion_year]`, edu.completion_year);
                            formData.append(`education_history[${idx}][gpa_grade]`, edu.gpa_grade);
                            formData.append(`education_history[${idx}][certificate_path]`, edu.certificate_path || '');
                        });
                    } else if (this.step === 5) {
                        formData.append('ict_description', this.form.ict_description);
                        this.form.ict_skills.forEach((s, idx) => {
                            formData.append(`ict_skills[${idx}][skill]`, s.skill);
                            formData.append(`ict_skills[${idx}][level]`, s.level);
                        });
                    } else if (this.step === 6) {
                        // Gather teaching experience details
                        if (this.positionType === 'teacher') {
                            this.form.teaching_subjects.forEach((subj, idx) => {
                                formData.append(`teaching_subjects[${idx}]`, subj);
                            });
                            formData.append('teaching_other_subjects', this.form.teaching_other_subjects);
                            formData.append('teaching_years', this.form.teaching_years);
                            formData.append('teaching_level', this.form.teaching_level);
                            formData.append('teaching_institution', this.form.teaching_institution);
                        }

                        // Predefined qualifications
                        this.form.professional_qualifications.forEach((q, idx) => {
                            formData.append(`qualifications[${idx}][name]`, q.name);
                            formData.append(`qualifications[${idx}][registration_number]`, q.registration_number);
                            formData.append(`qualifications[${idx}][date_issued]`, q.date_issued);
                            formData.append(`qualifications[${idx}][expiry_date]`, q.expiry_date || '');
                            formData.append(`qualifications[${idx}][certificate_path]`, q.certificate_path || '');
                        });

                        // Custom qualifications builder
                        this.customQualifications.forEach((q, idx) => {
                            const offset = this.form.professional_qualifications.length + idx;
                            formData.append(`qualifications[${offset}][name]`, q.name);
                            formData.append(`qualifications[${offset}][registration_number]`, q.registration_number);
                            formData.append(`qualifications[${offset}][date_issued]`, q.date_issued);
                            formData.append(`qualifications[${offset}][expiry_date]`, q.expiry_date || '');
                            formData.append(`qualifications[${offset}][certificate_path]`, q.certificate_path || '');
                        });
                    } else if (this.step === 7) {
                        formData.append('motivation_letter', this.form.motivation_letter);
                    } else if (this.step === 8) {
                        // Attachments are uploaded via onchange events, but we transition here
                    }

                    fetch(this.getUrl('/careers/apply/save-step'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        }
                        if (data.success) {
                            this.applicationId = data.application_id;
                            this.step++;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        } else {
                            if (data.errors) {
                                this.errorsList = Object.values(data.errors).flat();
                            } else {
                                this.errorsList = [data.message || 'Kosa limetokea wakati wa kuhifadhi.'];
                            }
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    })
                    .catch(err => {
                        this.errorsList = ['Hitilafu ya mtandao, tafadhali angalia muunganisho wako.'];
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                },

                submitFinal() {
                    this.errorsList = [];
                    // Final submit on step 9
                    const formData = new FormData();
                    formData.append('step', '9');
                    formData.append('vacancy_id', this.vacancyId);
                    formData.append('application_id', this.applicationId);
                    
                    const canvas = document.getElementById('signatureCanvas');
                    if (canvas) {
                        this.form.digital_signature = canvas.toDataURL();
                    }
                    formData.append('certified_correct', this.form.certified_correct ? '1' : '0');
                    formData.append('digital_signature', this.form.digital_signature);

                    fetch(this.getUrl('/careers/apply/save-step'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        }
                        if (data.success) {
                            window.location.href = data.redirect_url || this.getUrl('/careers/dashboard');
                        } else {
                            if (data.errors) {
                                this.errorsList = Object.values(data.errors).flat();
                            } else {
                                this.errorsList = [data.message || 'Kosa limetokea wakati wa kuwasilisha.'];
                            }
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    });
                }
            };
        }
    </script>
</x-public-layout>
