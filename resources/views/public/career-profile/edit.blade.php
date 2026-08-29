<x-public-layout title="Edit Career Profile">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden" 
             x-data="{ 
                 skills: {{ json_encode($profile->skills ?? []) }}, 
                 newSkill: '',
                 locations: {{ json_encode($profile->preferred_locations ?? []) }},
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
                <h1 class="text-2xl font-black">Edit Your Career Profile</h1>
                <p class="text-slate-400 text-xs font-semibold">Update your professional details, CV, and job preferences.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('career.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8 sm:p-10 space-y-8 text-xs font-semibold text-slate-700">
                @csrf
                @method('PUT')

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

                <!-- Professional Details -->
                <div class="space-y-6">
                    <h3 class="text-slate-900 font-extrabold text-sm border-b pb-3">Professional Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">Current Profession / Job Title</label>
                            <input type="text" name="current_profession" value="{{ old('current_profession', $profile->current_profession) }}" required
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-500">Years of Experience</label>
                            <input type="number" name="years_experience" value="{{ old('years_experience', $profile->years_experience) }}" required min="0"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                        </div>
                    </div>

                    <!-- Skills tags builder -->
                    <div class="space-y-2">
                        <label class="block text-slate-500">Core Skills & Expertise</label>
                        <div class="flex gap-3">
                            <input type="text" x-model="newSkill" @keydown.enter.prevent="addSkill()" placeholder="Type a skill and click Add"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950 placeholder-slate-400">
                            <button type="button" @click="addSkill()" class="px-5 py-4 rounded-2xl border border-slate-200 hover:bg-slate-50 font-extrabold">
                                Add
                            </button>
                        </div>
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
                        </div>
                    </div>
                </div>

                <!-- CV and Social Links -->
                <div class="space-y-6 pt-6 border-t">
                    <h3 class="text-slate-900 font-extrabold text-sm border-b pb-3">CV & Links</h3>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                        <div class="space-y-2">
                            <label class="block text-slate-500">Update CV File (Leave empty to keep existing CV. MIME: PDF, DOC, DOCX. Max: 5MB)</label>
                            <input type="file" name="cv_file" class="w-full p-3 bg-white rounded-xl border border-slate-200 text-slate-950">
                            <p class="text-[10px] text-slate-400">Uploading a new CV will replace the old CV in our private storage vault.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">LinkedIn Profile URL (Optional)</label>
                            <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" placeholder="https://linkedin.com/in/username"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-500">Portfolio / Website URL (Optional)</label>
                            <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $profile->portfolio_url) }}" placeholder="https://myportfolio.com"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                </div>

                <!-- Preferences -->
                <div class="space-y-6 pt-6 border-t">
                    <h3 class="text-slate-900 font-extrabold text-sm border-b pb-3">Job Preferences</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">Preferred Locations</label>
                            <div class="flex gap-3">
                                <input type="text" x-model="newLocation" @keydown.enter.prevent="addLocation()" placeholder="e.g. Dar es Salaam"
                                       class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950">
                                <button type="button" @click="addLocation()" class="px-5 py-4 rounded-2xl border border-slate-200 hover:bg-slate-50 font-extrabold">
                                    Add
                                </button>
                            </div>
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
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-slate-500">Expected Salary (Monthly TZS, Optional)</label>
                            <input type="number" name="expected_salary" value="{{ old('expected_salary', $profile->expected_salary) }}" placeholder="e.g. 1500000"
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-slate-500">Availability Date</label>
                            <input type="date" name="availability_date" value="{{ old('availability_date', $profile->availability_date ? $profile->availability_date->format('Y-m-d') : '') }}" required
                                   class="w-full p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="flex justify-between items-center pt-8 border-t bg-white">
                    <a href="{{ route('career.profile.show') }}" class="px-6 py-3.5 rounded-2xl border border-slate-200 hover:bg-slate-50 font-extrabold block">
                        Cancel
                    </a>
                    <button type="submit" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold shadow-lg hover:shadow-xl transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
