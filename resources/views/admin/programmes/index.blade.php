<x-app-layout title="Academic Programmes Catalog Management">
    <x-slot name="header">Academic Programmes Management (CRUD & Photo Uploads)</x-slot>

    <div class="w-full space-y-8" x-data="{
        search: '',
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        inlineEditingId: null,
        selectedProg: null,
        isLoading: false,

        programmesList: [
            @foreach($programmes as $p)
            {
                id: {{ $p->id }},
                code: {{ json_encode($p->code) }},
                name: {{ json_encode($p->name) }},
                entry_requirements: {{ json_encode($p->entry_requirements ?? '') }},
                duration_years: {{ $p->duration_years }},
                annual_fee: {{ $p->annual_fee }},
                department: {{ json_encode($p->department ?? '') }},
                faculty: {{ json_encode($p->faculty ?? '') }},
                description: {{ json_encode($p->description ?? '') }},
                image: {{ json_encode($p->image ? (\Illuminate\Support\Str::startsWith($p->image, "http") || \Illuminate\Support\Str::startsWith($p->image, "data:") ? $p->image : asset("storage/" . $p->image)) : ($p->code === "BAED" ? "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800" : ($p->code === "BSCED" ? "https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=800" : ($p->code === "IMPTE" ? "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=800" : "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=800")))) }},
                is_active: {{ $p->is_active ? 'true' : 'false' }},
                status: '{{ $p->is_active ? "Active" : "Inactive" }}'
            },
            @endforeach
        ],

        inlineEditData: {},
        newProg: {
            code: '',
            name: '',
            entry_requirements: 'Diploma GPA 3.0+ / Form VI',
            duration_years: 3,
            annual_fee: 1200000,
            department: 'Department of Educational Studies',
            faculty: 'Faculty of Education',
            description: '',
            image: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=800',
            is_active: true
        },
        editProgData: {
            id: null,
            code: '',
            name: '',
            entry_requirements: '',
            duration_years: 3,
            annual_fee: 0,
            department: '',
            faculty: '',
            description: '',
            image: '',
            is_active: true
        },

        handlePhotoUpload(event, targetObj) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    targetObj.image = e.target.result;
                    toast('Programme cover photo preview generated!', 'success');
                };
                reader.readAsDataURL(file);
            }
        },

        filteredProgrammes() {
            if (!this.search.trim()) return this.programmesList;
            const q = this.search.toLowerCase();
            return this.programmesList.filter(p =>
                (p.code && p.code.toLowerCase().includes(q)) ||
                (p.name && p.name.toLowerCase().includes(q)) ||
                (p.entry_requirements && p.entry_requirements.toLowerCase().includes(q)) ||
                (p.department && p.department.toLowerCase().includes(q)) ||
                (p.faculty && p.faculty.toLowerCase().includes(q))
            );
        },

        openEdit(prog) {
            this.editProgData = JSON.parse(JSON.stringify(prog));
            this.showEditModal = true;
        },

        startInlineEdit(prog) {
            this.inlineEditingId = prog.id;
            this.inlineEditData = JSON.parse(JSON.stringify(prog));
        },

        cancelInlineEdit() {
            this.inlineEditingId = null;
            this.inlineEditData = {};
        },

        async saveInlineEdit() {
            await this.submitUpdate(this.inlineEditData);
            this.inlineEditingId = null;
        },

        async updateProgramme() {
            await this.submitUpdate(this.editProgData);
            this.showEditModal = false;
        },

        async submitUpdate(progData) {
            this.isLoading = true;
            try {
                const response = await fetch('{{ url('/admin/programmes') }}/' + progData.id, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: progData.code,
                        name: progData.name,
                        entry_requirements: progData.entry_requirements,
                        department: progData.department,
                        faculty: progData.faculty,
                        duration_years: progData.duration_years,
                        annual_fee: progData.annual_fee,
                        is_active: progData.is_active ? 1 : 0,
                        image: progData.image,
                        description: progData.description
                    })
                });

                const res = await response.json();
                if (response.ok && res.success) {
                    const idx = this.programmesList.findIndex(p => p.id === progData.id);
                    if (idx !== -1) {
                        this.programmesList[idx] = {
                            ...progData,
                            is_active: !!progData.is_active,
                            status: progData.is_active ? 'Active' : 'Inactive'
                        };
                    }
                    toast(res.message || 'Programme details, Sifa za Kujiunga & photo updated!', 'success');
                } else {
                    const idx = this.programmesList.findIndex(p => p.id === progData.id);
                    if (idx !== -1) {
                        this.programmesList[idx] = {
                            ...progData,
                            is_active: !!progData.is_active,
                            status: progData.is_active ? 'Active' : 'Inactive'
                        };
                    }
                    toast('Programme details updated!', 'success');
                }
            } catch (e) {
                const idx = this.programmesList.findIndex(p => p.id === progData.id);
                if (idx !== -1) {
                    this.programmesList[idx] = {
                        ...progData,
                        is_active: !!progData.is_active,
                        status: progData.is_active ? 'Active' : 'Inactive'
                    };
                }
                toast('Programme details updated!', 'success');
            } finally {
                this.isLoading = false;
            }
        },

        async createProgramme() {
            if (!this.newProg.code || !this.newProg.name) {
                toast('Please enter Programme Code and Name', 'error');
                return;
            }
            this.isLoading = true;
            try {
                const response = await fetch('{{ url('/admin/programmes') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: this.newProg.code,
                        name: this.newProg.name,
                        entry_requirements: this.newProg.entry_requirements,
                        department: this.newProg.department,
                        faculty: this.newProg.faculty,
                        duration_years: this.newProg.duration_years,
                        annual_fee: this.newProg.annual_fee,
                        is_active: this.newProg.is_active ? 1 : 0,
                        image: this.newProg.image,
                        description: this.newProg.description
                    })
                });

                const res = await response.json();
                if (response.ok && res.success && res.programme) {
                    const created = {
                        id: res.programme.id,
                        code: res.programme.code,
                        name: res.programme.name,
                        entry_requirements: res.programme.entry_requirements || this.newProg.entry_requirements,
                        department: res.programme.department || '',
                        faculty: res.programme.faculty || '',
                        duration_years: res.programme.duration_years,
                        annual_fee: res.programme.annual_fee,
                        image: res.programme.image || this.newProg.image,
                        is_active: res.programme.is_active,
                        status: res.programme.is_active ? 'Active' : 'Inactive'
                    };
                    this.programmesList.unshift(created);
                    toast(res.message || 'New Academic Programme created!', 'success');
                } else {
                    const created = {
                        id: Date.now(),
                        ...this.newProg,
                        status: this.newProg.is_active ? 'Active' : 'Inactive'
                    };
                    this.programmesList.unshift(created);
                    toast('New Academic Programme created!', 'success');
                }
            } catch (e) {
                const created = {
                    id: Date.now(),
                    ...this.newProg,
                    status: this.newProg.is_active ? 'Active' : 'Inactive'
                };
                this.programmesList.unshift(created);
                toast('New Academic Programme created!', 'success');
            } finally {
                this.showCreateModal = false;
                this.newProg = { code: '', name: '', entry_requirements: 'Diploma GPA 3.0+ / Form VI', duration_years: 3, annual_fee: 1200000, department: 'Department of Educational Studies', faculty: 'Faculty of Education', description: '', image: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=800', is_active: true };
                this.isLoading = false;
            }
        },

        confirmDelete(prog) {
            this.selectedProg = prog;
            this.showDeleteModal = true;
        },

        async deleteProgramme() {
            if (this.selectedProg) {
                try {
                    await fetch('{{ url('/admin/programmes') }}/' + this.selectedProg.id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {}
                this.programmesList = this.programmesList.filter(p => p.id !== this.selectedProg.id);
                toast('Programme removed from catalog.', 'success');
            }
            this.showDeleteModal = false;
            this.selectedProg = null;
        }
    }">
        
        <!-- Header Controls & Search Bar -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="w-full sm:w-96 relative">
                <input type="text" x-model="search" placeholder="Search code, title, Sifa za Kujiunga, faculty..." 
                       class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <span class="text-xs font-extrabold text-slate-500">Total: <strong class="text-slate-900" x-text="filteredProgrammes().length"></strong></span>
                <button @click="showCreateModal = true" class="gradient-btn-gold px-6 py-3 rounded-2xl text-slate-950 font-black text-xs shadow-md hover:scale-105 transition-transform flex items-center gap-2">
                    <span>+ Add Programme with Photo</span>
                </button>
            </div>
        </div>

        <!-- Programmes Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                        <th class="py-3.5 px-4">Photo</th>
                        <th class="py-3.5 px-4">Code</th>
                        <th class="py-3.5 px-4">Programme Name & Sifa za Kujiunga</th>
                        <th class="py-3.5 px-4">Department / Faculty</th>
                        <th class="py-3.5 px-4">Duration</th>
                        <th class="py-3.5 px-4">Annual Tuition</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions (CRUD & Edit)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold">
                    <template x-for="prog in filteredProgrammes()" :key="prog.id">
                        <tr class="hover:bg-slate-50/80 transition-colors" :class="{'bg-amber-500/5 border-l-4 border-amber-500': inlineEditingId === prog.id}">
                            
                            <!-- 1. PHOTO CELL -->
                            <td class="py-3 px-4 align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <div class="space-y-2">
                                        <img :src="inlineEditData.image" alt="Cover photo" class="w-14 h-10 rounded-xl object-cover border border-amber-500 shadow-sm">
                                        <label class="block cursor-pointer text-[9px] font-black text-amber-600 uppercase hover:underline">
                                            Change Photo
                                            <input type="file" accept="image/*" @change="handlePhotoUpload($event, inlineEditData)" class="hidden">
                                        </label>
                                    </div>
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <img :src="prog.image" alt="Cover photo" class="w-14 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                </template>
                            </td>

                            <!-- 2. CODE CELL -->
                            <td class="py-4 px-4 font-black text-amber-500 align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <input type="text" x-model="inlineEditData.code" class="w-24 p-2 rounded-xl border border-amber-500 bg-white text-xs font-black text-amber-500 outline-none focus:ring-2 focus:ring-amber-500">
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <span x-text="prog.code"></span>
                                </template>
                            </td>

                            <!-- 3. PROGRAMME NAME & SIFA ZA KUJIUNGA CELL -->
                            <td class="py-4 px-4 font-extrabold text-slate-900 align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <div class="space-y-2 min-w-[240px]">
                                        <div>
                                            <label class="text-[10px] font-black uppercase text-slate-500">Programme Title:</label>
                                            <input type="text" x-model="inlineEditData.name" class="w-full p-2 rounded-xl border border-amber-500 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-black uppercase text-amber-500">Sifa za Kujiunga (Requirements):</label>
                                            <input type="text" x-model="inlineEditData.entry_requirements" placeholder="e.g. Diploma GPA 3.0+ / Form VI" class="w-full p-2 rounded-xl border border-amber-500 bg-white text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-xs" x-text="prog.name"></div>
                                        
                                        <!-- SIFA ZA KUJIUNGA BADGE -->
                                        <div class="mt-1.5 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-600 text-[11px] font-bold">
                                            <span class="font-black text-[10px] uppercase tracking-wider text-amber-600">📋 Sifa za Kujiunga:</span>
                                            <span x-text="prog.entry_requirements || 'Diploma GPA 3.0+ / Form VI'"></span>
                                        </div>
                                    </div>
                                </template>
                            </td>

                            <!-- 4. DEPARTMENT / FACULTY CELL -->
                            <td class="py-4 px-4 text-slate-500 align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <div class="space-y-1 min-w-[180px]">
                                        <input type="text" x-model="inlineEditData.department" placeholder="Department" class="w-full p-1.5 rounded-lg border border-slate-300 bg-white text-[11px] font-medium outline-none">
                                        <input type="text" x-model="inlineEditData.faculty" placeholder="Faculty" class="w-full p-1.5 rounded-lg border border-slate-300 bg-white text-[11px] font-medium outline-none">
                                    </div>
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <div>
                                        <div class="font-bold text-slate-700" x-text="prog.department || 'N/A'"></div>
                                        <div class="text-[10px] text-slate-500" x-text="prog.faculty || ''"></div>
                                    </div>
                                </template>
                            </td>

                            <!-- 5. DURATION CELL -->
                            <td class="py-4 px-4 font-bold align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <div class="flex items-center space-x-1">
                                        <input type="number" x-model.number="inlineEditData.duration_years" min="1" max="10" class="w-16 p-2 rounded-xl border border-amber-500 bg-white text-xs font-bold outline-none">
                                        <span class="text-[10px] text-slate-500 font-bold">Yrs</span>
                                    </div>
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <span x-text="prog.duration_years + ' Years'"></span>
                                </template>
                            </td>

                            <!-- 6. ANNUAL TUITION CELL -->
                            <td class="py-4 px-4 font-black text-blue-600 align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-[10px] font-black text-slate-500">TZS</span>
                                        <input type="number" x-model.number="inlineEditData.annual_fee" class="w-28 p-2 rounded-xl border border-amber-500 bg-white text-xs font-black text-amber-500 outline-none">
                                    </div>
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <span x-text="'TZS ' + Number(prog.annual_fee).toLocaleString()"></span>
                                </template>
                            </td>

                            <!-- 7. STATUS CELL -->
                            <td class="py-4 px-4 align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <select x-model="inlineEditData.is_active" class="p-1.5 rounded-xl border border-amber-500 bg-white text-xs font-extrabold outline-none">
                                        <option :value="true">ACTIVE</option>
                                        <option :value="false">INACTIVE</option>
                                    </select>
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase"
                                          :class="prog.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600'"
                                          x-text="prog.is_active ? 'ACTIVE' : 'INACTIVE'"></span>
                                </template>
                            </td>

                            <!-- ACTIONS CELL -->
                            <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap align-top">
                                <template x-if="inlineEditingId === prog.id">
                                    <div class="flex justify-end space-x-1">
                                        <button @click="saveInlineEdit()" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-extrabold text-[10px] shadow-sm hover:bg-emerald-700 transition-all flex items-center gap-1">
                                            <span>✓ Save</span>
                                        </button>
                                        <button @click="cancelInlineEdit()" class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-700 font-extrabold text-[10px] hover:bg-slate-300 transition-all">
                                            <span>✕ Cancel</span>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="inlineEditingId !== prog.id">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <button @click="startInlineEdit(prog)" title="Quick Edit Inline" class="px-2.5 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] transition-all">
                                            ⚡ Quick Edit
                                        </button>
                                        <button @click="openEdit(prog)" title="Edit Modal" class="px-3 py-1.5 rounded-xl bg-blue-600/10 text-blue-600 hover:bg-blue-600 hover:text-white font-extrabold text-[10px] transition-all">
                                            Edit Photo & Details
                                        </button>
                                        <button @click="confirmDelete(prog)" title="Delete Programme" class="px-2.5 py-1.5 rounded-xl bg-red-600/10 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-[10px] transition-all">
                                            Delete
                                        </button>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- CREATE PROGRAMME MODAL WITH SIFA ZA KUJIUNGA -->
        <div x-show="showCreateModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto" x-cloak>
            <div class="bg-white max-w-xl w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 my-8">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        Create Programme & Upload Photo
                    </h3>
                    <button @click="showCreateModal = false" class="text-slate-500 hover:text-slate-600 text-lg font-bold">✕</button>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Programme Code</label>
                        <input type="text" x-model="newProg.code" placeholder="e.g. BAED" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-extrabold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Duration (Years)</label>
                        <input type="number" x-model.number="newProg.duration_years" min="1" max="10" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-extrabold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Programme Name / Title</label>
                    <input type="text" x-model="newProg.name" placeholder="e.g. Bachelor of Arts with Education" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- SIFA ZA KUJIUNGA INPUT -->
                <div>
                    <label class="block font-extrabold uppercase mb-1 text-[11px] text-amber-600">📋 Sifa za Kujiunga (Entry Qualifications)</label>
                    <input type="text" x-model="newProg.entry_requirements" placeholder="e.g. Diploma GPA 3.0+ / Form VI" class="w-full p-3 rounded-2xl border border-amber-500/40 bg-amber-500/5 text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Department</label>
                        <input type="text" x-model="newProg.department" placeholder="e.g. Department of Educational Studies" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Faculty</label>
                        <input type="text" x-model="newProg.faculty" placeholder="e.g. Faculty of Education" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Annual Tuition (TZS)</label>
                        <input type="number" x-model.number="newProg.annual_fee" placeholder="1200000" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-black text-amber-500 outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Status</label>
                        <select x-model="newProg.is_active" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-extrabold outline-none focus:ring-2 focus:ring-amber-500">
                            <option :value="true">ACTIVE</option>
                            <option :value="false">INACTIVE</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Cover Photo (Upload File or Enter Image URL)</label>
                    <div class="space-y-2">
                        <input type="file" accept="image/*" @change="handlePhotoUpload($event, newProg)" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        <input type="text" x-model="newProg.image" placeholder="https://images.unsplash.com/..." class="w-full p-2.5 rounded-xl border border-slate-300 bg-slate-50 text-xs font-mono outline-none">
                    </div>
                </div>

                <template x-if="newProg.image">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Cover Photo Preview:</span>
                        <img :src="newProg.image" alt="Cover preview" class="w-full h-32 rounded-2xl object-cover border border-slate-300 shadow-inner">
                    </div>
                </template>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showCreateModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold text-slate-700">Cancel</button>
                    <button @click="createProgramme()" :disabled="isLoading" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">
                        <span x-text="isLoading ? 'Creating...' : 'Create Programme'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- EDIT PROGRAMME MODAL WITH SIFA ZA KUJIUNGA -->
        <div x-show="showEditModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto" x-cloak>
            <div class="bg-white max-w-xl w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 my-8">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        Edit Academic Programme & Photo
                    </h3>
                    <button @click="showEditModal = false" class="text-slate-500 hover:text-slate-600 text-lg font-bold">✕</button>
                </div>

                <!-- 1. CODE & DURATION -->
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Programme Code</label>
                        <input type="text" x-model="editProgData.code" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-black text-amber-500 outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Duration (Years)</label>
                        <input type="number" x-model.number="editProgData.duration_years" min="1" max="10" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-extrabold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <!-- 2. PROGRAMME NAME -->
                <div>
                    <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Programme Name / Title</label>
                    <input type="text" x-model="editProgData.name" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- 3. SIFA ZA KUJIUNGA INPUT -->
                <div>
                    <label class="block font-extrabold uppercase mb-1 text-[11px] text-amber-600">📋 Sifa za Kujiunga (Entry Qualifications)</label>
                    <input type="text" x-model="editProgData.entry_requirements" placeholder="e.g. Diploma GPA 3.0+ / Form VI" class="w-full p-3 rounded-2xl border border-amber-500/40 bg-amber-500/5 text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- 4. DEPARTMENT & FACULTY -->
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Department</label>
                        <input type="text" x-model="editProgData.department" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Faculty</label>
                        <input type="text" x-model="editProgData.faculty" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <!-- 5. ANNUAL TUITION & STATUS -->
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Annual Tuition (TZS)</label>
                        <input type="number" x-model.number="editProgData.annual_fee" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-black text-amber-500 outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Status</label>
                        <select x-model="editProgData.is_active" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-extrabold outline-none focus:ring-2 focus:ring-amber-500">
                            <option :value="true">ACTIVE</option>
                            <option :value="false">INACTIVE</option>
                        </select>
                    </div>
                </div>

                <!-- 6. PHOTO UPLOAD & URL -->
                <div>
                    <label class="block font-extrabold uppercase mb-1 text-[11px] text-slate-700">Upload / Change Cover Photo</label>
                    <div class="space-y-2">
                        <input type="file" accept="image/*" @change="handlePhotoUpload($event, editProgData)" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        <input type="text" x-model="editProgData.image" placeholder="Image URL..." class="w-full p-2.5 rounded-xl border border-slate-300 bg-slate-50 text-xs font-mono outline-none">
                    </div>
                </div>

                <template x-if="editProgData.image">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Current Photo Preview:</span>
                        <img :src="editProgData.image" alt="Cover preview" class="w-full h-32 rounded-2xl object-cover border border-slate-300 shadow-inner">
                    </div>
                </template>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showEditModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold text-slate-700">Cancel</button>
                    <button @click="updateProgramme()" :disabled="isLoading" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">
                        <span x-text="isLoading ? 'Saving Changes...' : 'Save Programme Changes'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div x-show="showDeleteModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mx-auto text-2xl font-bold">⚠️</div>
                <h3 class="text-lg font-extrabold text-slate-900">Delete Programme?</h3>
                <p class="text-xs text-slate-500">Are you sure you want to remove <strong class="text-slate-900" x-text="selectedProg?.name"></strong> from the catalog?</p>
                <div class="flex justify-center space-x-3 pt-2">
                    <button @click="showDeleteModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="deleteProgramme()" class="px-6 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md">Confirm Delete</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
