<x-public-layout title="Complete Your Career Profile">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden" 
             x-data="{ 
                 step: 1, 
                 skills: [], 
                 newSkill: '',
                 locations: [],
                 newLocation: '',
                 addSkill() {
                     let val = this.newSkill.trim();
                     if (val && !this.skills.includes(val)) {
                         this.skills.push(val);
                     }
                     this.newSkill = '';
                 },
                 removeSkill(idx) {
                     this.skills.splice(idx, 1);
                 },
                 addLocation() {
                     let val = this.newLocation.trim();
                     if (val && !this.locations.includes(val)) {
                         this.locations.push(val);
                     }
                     this.newLocation = '';
                 },
                 removeLocation(idx) {
                     this.locations.splice(idx, 1);
                 }
             }">
             
            <!-- Header -->
            <div class="bg-slate-950 text-white p-8 text-center space-y-2">
                <h1 class="text-2xl font-black">Complete Your Career Profile</h1>
                <p class="text-slate-400 text-xs font-semibold">Your profile helps us match you with the right careers and external redirects.</p>
                
                <!-- Progress Indicators -->
                <div class="flex items-center justify-center gap-4 pt-6 max-w-md mx-auto">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-200"
                              :class="step === 1 ? 'bg-amber-500 text-slate-950 scale-110 shadow-lg' : (step > 1 ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400')">1</span>
                        <span class="text-[10px] uppercase font-bold" :class="step === 1 ? 'text-white' : 'text-slate-500'">Professional</span>
                    </div>
                    <div class="h-[2px] bg-slate-800 w-8"></div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-200"
                              :class="step === 2 ? 'bg-amber-500 text-slate-950 scale-110 shadow-lg' : (step > 2 ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400')">2</span>
                        <span class="text-[10px] uppercase font-bold" :class="step === 2 ? 'text-white' : 'text-slate-500'">CV & Links</span>
                    </div>
                    <div class="h-[2px] bg-slate-800 w-8"></div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-200"
                              :class="step === 3 ? 'bg-amber-500 text-slate-950 scale-110 shadow-lg' : 'bg-slate-800 text-slate-400'">3</span>
                        <span class="text-[10px] uppercase font-bold" :class="step === 3 ? 'text-white' : 'text-slate-500'">Preferences</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('career.profile.store') }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8 text-xs font-semibold text-slate-700">
                @csrf

                <!-- Display Errors -->
                @if($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl">
                        <p class="font-bold mb-2">Please fix the following validation errors:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- STEP 1: Professional Details -->
                <div x-show="step === 1" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">Current Profession / Job Title</label>
                            <input type="text" name="current_profession" value="{{ old('current_profession') }}" required placeholder="e.g. Senior Software Engineer"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-500">Years of Experience</label>
                            <input type="number" name="years_experience" value="{{ old('years_experience') }}" required min="0" placeholder="e.g. 5"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                        </div>
                    </div>

                    <!-- Skills tags builder -->
                    <div class="space-y-2">
                        <label class="block text-slate-500">Core Skills & Expertise</label>
                        <div class="flex gap-3">
                            <input type="text" x-model="newSkill" @keydown.enter.prevent="addSkill()" placeholder="Type a skill and click Add (e.g. PHP, Laravel)"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                            <button type="button" @click="addSkill()" class="px-5 py-4 rounded-2xl border border-slate-200 hover:bg-slate-50 font-extrabold">
                                Add
                            </button>
                        </div>
                        <!-- Hidden inputs for array submission -->
                        <template x-for="(skill, index) in skills" :key="index">
                            <input type="hidden" name="skills[]" :value="skill">
                        </template>
                        
                        <div class="flex flex-wrap gap-2 pt-3">
                            <template x-for="(skill, index) in skills" :key="index">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-600 font-extrabold text-[10px] uppercase">
                                    <span x-text="skill"></span>
                                    <button type="button" @click="removeSkill(index)" class="text-blue-400 hover:text-blue-600 text-xs font-black">&times;</button>
                                </span>
                            </template>
                            <span x-show="skills.length === 0" class="text-slate-400 font-normal italic">No skills added yet.</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: CV and Social Links -->
                <div x-show="step === 2" class="space-y-6" x-cloak>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                        <h3 class="text-slate-900 font-extrabold text-sm border-b pb-3">Upload Curriculum Vitae (CV)</h3>
                        <div class="space-y-2">
                            <label class="block text-slate-500">Choose CV File (MIME: PDF, DOC, DOCX. Max: 5MB)</label>
                            <input type="file" name="cv_file" required class="w-full p-3 bg-white rounded-xl border border-slate-200 text-slate-950">
                            <p class="text-[10px] text-slate-400">Your CV will be stored securely in our private vault.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">LinkedIn Profile URL (Optional)</label>
                            <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/username"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-500">Portfolio / Website URL (Optional)</label>
                            <input type="url" name="portfolio_url" value="{{ old('portfolio_url') }}" placeholder="https://myportfolio.com"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Preferences -->
                <div x-show="step === 3" class="space-y-6" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">Preferred Locations</label>
                            <div class="flex gap-3">
                                <input type="text" x-model="newLocation" @keydown.enter.prevent="addLocation()" placeholder="e.g. Dar es Salaam"
                                       class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                                <button type="button" @click="addLocation()" class="px-5 py-4 rounded-2xl border border-slate-200 hover:bg-slate-50 font-extrabold">
                                    Add
                                </button>
                            </div>
                            <!-- Hidden inputs for locations -->
                            <template x-for="(loc, index) in locations" :key="index">
                                <input type="hidden" name="preferred_locations[]" :value="loc">
                            </template>

                            <div class="flex flex-wrap gap-2 pt-3">
                                <template x-for="(loc, index) in locations" :key="index">
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-extrabold text-[10px] uppercase">
                                        <span x-text="loc"></span>
                                        <button type="button" @click="removeLocation(index)" class="text-slate-400 hover:text-slate-600 text-xs font-black">&times;</button>
                                    </span>
                                </template>
                                <span x-show="locations.length === 0" class="text-slate-400 font-normal italic">No locations added yet.</span>
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">Expected Salary (Monthly TZS, Optional)</label>
                            <input type="number" name="expected_salary" value="{{ old('expected_salary') }}" placeholder="e.g. 1500000"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-500">Availability Date</label>
                            <input type="date" name="availability_date" value="{{ old('availability_date') }}" required
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="flex justify-between items-center pt-8 border-t sticky bottom-0 bg-white">
                    <button type="button" x-show="step > 1" x-cloak @click="step--" class="px-6 py-3.5 rounded-2xl border border-slate-200 hover:bg-slate-50 font-extrabold">
                        &larr; Back
                    </button>
                    <div x-show="step === 1"></div> <!-- Spacer if step is 1 -->
                    
                    <button type="button" x-show="step < 3" @click="step++" class="gradient-btn px-6 py-3.5 rounded-2xl text-white font-extrabold">
                        Next &rarr;
                    </button>
                    
                    <button type="submit" x-show="step === 3" x-cloak class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold shadow-lg hover:shadow-xl transition-all">
                        Complete Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
