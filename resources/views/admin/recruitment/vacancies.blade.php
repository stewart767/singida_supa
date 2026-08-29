<x-app-layout title="Vacancies Management">
    <x-slot name="header">Vacancies</x-slot>

    <div class="w-full space-y-8" x-data="{ addModal: false, editModal: false, addApplicationType: 'internal', editData: { id: '', job_title: '', department_name: '', designation_id: '', position_id: '', job_category_id: '', campus_id: '', number_of_positions: '1', employment_type: 'Full-time', contract_type: 'Permanent', location: 'Singida', recommended_region: '', salary_range: '', application_deadline: '', closing_date: '', responsibilities: '', qualifications: '', required_experience: '', required_skills: '', benefits: '', status: 'Draft', application_type: 'internal', external_url: '', external_provider: '' } }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h2 class="text-base font-extrabold text-slate-800">Manage Job Vacancies</h2>
            <button @click="addModal = true" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                + Create Vacancy
            </button>
        </div>

        <!-- Vacancies Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Vacancy #</th>
                            <th class="py-3.5 px-4">Job Title</th>
                            <th class="py-3.5 px-4">Department</th>
                            <th class="py-3.5 px-4">Designation</th>
                            <th class="py-3.5 px-4">Position</th>
                            <th class="py-3.5 px-4">Campus</th>
                            <th class="py-3.5 px-4">Region</th>
                            <th class="py-3.5 px-4">Quantity</th>
                            <th class="py-3.5 px-4">Deadline</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($vacancies as $vac)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-black text-amber-500">{{ $vac->vacancy_number }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $vac->job_title }}</td>
                                <td class="py-4 px-4 text-slate-500 font-bold">{{ $vac->department_name ?? 'N/A' }}</td>
                                <td class="py-4 px-4">{{ $vac->designation->name ?? 'N/A' }}</td>
                                <td class="py-4 px-4">{{ $vac->position->name ?? 'N/A' }}</td>
                                <td class="py-4 px-4 text-slate-500 font-bold">{{ $vac->campus->name ?? 'All Campuses' }}</td>
                                <td class="py-4 px-4 text-slate-500 font-bold">{{ $vac->recommended_region ?? 'N/A' }}</td>
                                <td class="py-4 px-4 font-black">{{ $vac->number_of_positions }}</td>
                                <td class="py-4 px-4 text-red-500 font-bold">{{ $vac->application_deadline->format('d M Y') }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $vac->status === 'Published' ? 'bg-emerald-100 text-emerald-800' : ($vac->status === 'Closed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $vac->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($vac->status === 'Draft')
                                            <form action="{{ route('admin.recruitment.vacancies.status', [$vac->id, 'publish']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-[11px] font-bold transition-all duration-200">Publish</button>
                                            </form>
                                        @endif
                                        @if($vac->status === 'Published')
                                            <form action="{{ route('admin.recruitment.vacancies.status', [$vac->id, 'close']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 text-[11px] font-bold transition-all duration-200">Close</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.recruitment.vacancies.status', [$vac->id, 'duplicate']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-[11px] font-bold transition-all duration-200">Duplicate</button>
                                        </form>
                                        <button @click="editData = {
                                            id: {{ json_encode($vac->id) }},
                                            job_title: {{ json_encode($vac->job_title) }},
                                            department_name: {{ json_encode($vac->department_name ?? '') }},
                                            designation_id: {{ json_encode($vac->designation_id) }},
                                            position_id: {{ json_encode($vac->position_id) }},
                                            job_category_id: {{ json_encode($vac->job_category_id) }},
                                            campus_id: {{ json_encode($vac->campus_id) }},
                                            number_of_positions: {{ json_encode($vac->number_of_positions) }},
                                            employment_type: {{ json_encode($vac->employment_type) }},
                                            contract_type: {{ json_encode($vac->contract_type) }},
                                            location: {{ json_encode($vac->location) }},
                                            recommended_region: {{ json_encode($vac->recommended_region ?? '') }},
                                            salary_range: {{ json_encode($vac->salary_range ?? '') }},
                                            application_deadline: {{ json_encode($vac->application_deadline->format('Y-m-d')) }},
                                            closing_date: {{ json_encode($vac->closing_date ? $vac->closing_date->format('Y-m-d') : '') }},
                                            responsibilities: {{ json_encode($vac->responsibilities) }},
                                            qualifications: {{ json_encode($vac->qualifications) }},
                                            required_experience: {{ json_encode($vac->required_experience) }},
                                            required_skills: {{ json_encode($vac->required_skills) }},
                                            benefits: {{ json_encode($vac->benefits ?? '') }},
                                            status: {{ json_encode($vac->status) }},
                                            application_type: {{ json_encode($vac->application_type ?? 'internal') }},
                                            external_url: {{ json_encode($vac->external_url ?? '') }},
                                            external_provider: {{ json_encode($vac->external_provider ?? '') }}
                                        }; editModal = true" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 text-[11px] font-bold transition-all duration-200">Edit</button>
                                        <form action="{{ route('admin.recruitment.vacancies.destroy', $vac->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this vacancy? This will also delete all associated applications!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-[11px] font-bold transition-all duration-200">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-500">No vacancies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Vacancy Modal -->
        <div x-show="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm overflow-y-auto" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-2xl p-6 border border-slate-200 shadow-2xl space-y-4 my-8">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Create New Vacancy</h3>
                    <button @click="addModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.vacancies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold max-h-[70vh] overflow-y-auto pr-2">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Job Title</label>
                            <input type="text" name="job_title" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Department Name</label>
                            <input type="text" name="department_name" required placeholder="e.g. Computer Science, Human Resources" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Designation</label>
                            <select name="designation_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">Select Designation</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Position</label>
                            <select name="position_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">Select Position</option>
                                @foreach($positions as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Campus Assignment</label>
                            <select name="campus_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">All Campuses</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Quantity</label>
                            <input type="number" name="number_of_positions" value="1" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Employment Type</label>
                            <select name="employment_type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Contract Type</label>
                            <select name="contract_type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Permanent">Permanent</option>
                                <option value="Fixed-term">Fixed-term</option>
                                <option value="Casual">Casual</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Location</label>
                            <input type="text" name="location" value="Singida" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Recommended Region</label>
                            <input type="text" name="recommended_region" list="existing_regions_list" value="Singida" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950" placeholder="e.g. Singida, Dar es Salaam">
                            <datalist id="existing_regions_list">
                                @foreach($existingRegions as $region)
                                    <option value="{{ $region }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Salary Range (Optional)</label>
                            <input type="text" name="salary_range" placeholder="e.g. TZS 1.2M - 1.8M" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Application Deadline</label>
                            <input type="date" name="application_deadline" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Closing Date</label>
                            <input type="date" name="closing_date" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Application Type</label>
                            <select name="application_type" x-model="addApplicationType" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="internal">Internal (Standard ATS)</option>
                                <option value="external">External (Redirect tracking)</option>
                            </select>
                        </div>
                        <div class="space-y-1.5 col-span-2" x-show="addApplicationType === 'external'">
                            <label class="block text-slate-500">External Application URL</label>
                            <input type="url" name="external_url" placeholder="https://ajiramarket.co.tz/jobs/..." class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4" x-show="addApplicationType === 'external'">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">External Provider</label>
                            <input type="text" name="external_provider" placeholder="ajiramarket" value="ajiramarket" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Job Responsibilities & Requirements</label>
                        <textarea name="responsibilities" rows="3" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950" placeholder="List the job responsibilities and requirements here..."></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Qualifications</label>
                        <textarea name="qualifications" rows="3" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">Bachelor's degree or equivalent in the related field.</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Required Experience</label>
                            <textarea name="required_experience" rows="2" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">At least 2 years of working experience in a similar role.</textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Required Skills</label>
                            <textarea name="required_skills" rows="2" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">Strong communication, teamwork, and relevant technical expertise.</textarea>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Benefits</label>
                        <textarea name="benefits" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950" placeholder="Optional benefits..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Featured Image</label>
                            <input type="file" name="featured_image_file" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Status</label>
                            <select name="status" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t sticky bottom-0 bg-white">
                        <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Vacancy</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Vacancy Modal -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm overflow-y-auto" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-2xl p-6 border border-slate-200 shadow-2xl space-y-4 my-8">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Edit Vacancy</h3>
                    <button @click="editModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form :action="'{{ url('/admin/recruitment/vacancies') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold max-h-[70vh] overflow-y-auto pr-2">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Job Title</label>
                            <input type="text" name="job_title" x-model="editData.job_title" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Department Name</label>
                            <input type="text" name="department_name" x-model="editData.department_name" required placeholder="e.g. Computer Science, Human Resources" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Designation</label>
                            <select name="designation_id" x-model="editData.designation_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Position</label>
                            <select name="position_id" x-model="editData.position_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                @foreach($positions as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Campus Assignment</label>
                            <select name="campus_id" x-model="editData.campus_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">All Campuses</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Quantity</label>
                            <input type="number" name="number_of_positions" x-model="editData.number_of_positions" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Employment Type</label>
                            <select name="employment_type" x-model="editData.employment_type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Contract Type</label>
                            <select name="contract_type" x-model="editData.contract_type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Permanent">Permanent</option>
                                <option value="Fixed-term">Fixed-term</option>
                                <option value="Casual">Casual</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Location</label>
                            <input type="text" name="location" x-model="editData.location" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Recommended Region</label>
                            <input type="text" name="recommended_region" x-model="editData.recommended_region" list="existing_regions_list_edit" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950" placeholder="e.g. Singida, Dar es Salaam">
                            <datalist id="existing_regions_list_edit">
                                @foreach($existingRegions as $region)
                                    <option value="{{ $region }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Salary Range (Optional)</label>
                            <input type="text" name="salary_range" x-model="editData.salary_range" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Application Deadline</label>
                            <input type="date" name="application_deadline" x-model="editData.application_deadline" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Closing Date</label>
                            <input type="date" name="closing_date" x-model="editData.closing_date" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Application Type</label>
                            <select name="application_type" x-model="editData.application_type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="internal">Internal (Standard ATS)</option>
                                <option value="external">External (Redirect tracking)</option>
                            </select>
                        </div>
                        <div class="space-y-1.5 col-span-2" x-show="editData.application_type === 'external'">
                            <label class="block text-slate-500">External Application URL</label>
                            <input type="url" name="external_url" x-model="editData.external_url" placeholder="https://ajiramarket.co.tz/jobs/..." class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4" x-show="editData.application_type === 'external'">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">External Provider</label>
                            <input type="text" name="external_provider" x-model="editData.external_provider" placeholder="ajiramarket" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Job Responsibilities & Requirements</label>
                        <textarea name="responsibilities" x-model="editData.responsibilities" rows="3" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950" placeholder="List the job responsibilities and requirements here..."></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Qualifications</label>
                        <textarea name="qualifications" x-model="editData.qualifications" rows="3" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Required Experience</label>
                            <textarea name="required_experience" x-model="editData.required_experience" rows="2" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Required Skills</label>
                            <textarea name="required_skills" x-model="editData.required_skills" rows="2" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Benefits</label>
                        <textarea name="benefits" x-model="editData.benefits" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Featured Image</label>
                            <input type="file" name="featured_image_file" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Status</label>
                            <select name="status" x-model="editData.status" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                                <option value="Closed">Closed</option>
                                <option value="Archived">Archived</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t sticky bottom-0 bg-white">
                        <button type="button" @click="editModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Update Vacancy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
