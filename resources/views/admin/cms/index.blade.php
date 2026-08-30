<x-app-layout title="Website CMS & Media Upload Manager">
    <x-slot name="header">Website CMS & Photo Upload Control Desk</x-slot>

    <div class="w-full space-y-8" 
         @hashchange.window="updateTabFromHash()"
         x-data="cmsDesk">
        
        <!-- Interactive Primary Navigation Tabs -->
        <div class="bg-white p-2 rounded-3xl border border-slate-200 shadow-sm flex flex-wrap gap-2">
            @if(auth()->user()->hasPermissionTo('manage_settings'))
            <button @click="activeTab = 'cms'; window.location.hash = 'cms'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'cms' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>Website CMS</span>
            </button>
            <button @click="activeTab = 'media'; window.location.hash = 'media'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'media' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>Media Library</span>
            </button>
            <button @click="activeTab = 'users'; window.location.hash = 'users'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'users' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>Users & Roles</span>
            </button>
            @endif
            @if(auth()->user()->hasPermissionTo('view_reports'))
            <button @click="activeTab = 'reports'; window.location.hash = 'reports'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'reports' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>Reports & Analytics</span>
            </button>
            @endif
            @if(auth()->user()->hasPermissionTo('manage_settings'))
            <button @click="activeTab = 'comm'; window.location.hash = 'comm'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'comm' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>Communication Logs</span>
            </button>
            <button @click="activeTab = 'settings'; window.location.hash = 'settings'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'settings' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>System Settings</span>
            </button>
            <button @click="activeTab = 'contact'; window.location.hash = 'contact'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'contact' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>📩 Contact Messages</span>
                <span x-show="contactMessages.filter(c => !c.is_read).length > 0" 
                      class="px-2 py-0.5 rounded-full text-[10px] bg-red-500 text-white font-black animate-pulse" 
                      x-text="contactMessages.filter(c => !c.is_read).length"></span>
            </button>
            <button @click="activeTab = 'logs'; window.location.hash = 'logs'" 
                    class="px-5 py-3 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2"
                    :class="activeTab === 'logs' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                <span>Audit Logs</span>
            </button>
            @endif
        </div>

        <!-- 1. WEBSITE CMS TAB CONTENT WITH SUB-DESKS -->
        <div x-show="activeTab === 'cms'" class="space-y-6">
            
            <!-- CMS Sub-Desk Navigation Pills -->
            <div class="flex flex-wrap gap-3 p-1.5 rounded-2xl bg-slate-100 text-xs font-bold w-fit">
                <button @click="cmsSubTab = 'sliders'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'sliders' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    📸 Hero Sliders
                </button>
                <button @click="cmsSubTab = 'about'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'about' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    🏛️ About Section
                </button>
                <button @click="cmsSubTab = 'cta'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'cta' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    ✨ Journey Banner (CTA)
                </button>
                <button @click="cmsSubTab = 'programmes'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'programmes' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    🎓 Featured Programmes Showcase
                </button>
                <button @click="cmsSubTab = 'prog_categories'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'prog_categories' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    📁 Navigation Mega-Menu Categories
                </button>
                <button @click="cmsSubTab = 'news'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'news' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    📰 News & Announcements
                </button>
                <button @click="cmsSubTab = 'contact_settings'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'contact_settings' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    📞 Contact Page Info
                </button>
                <button @click="cmsSubTab = 'footer'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'footer' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    🦶 Footer & Social Links
                </button>
                <button @click="cmsSubTab = 'logo'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'logo' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    🏷️ Logo & Brand Identity
                </button>
                <button @click="cmsSubTab = 'banners'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'banners' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    🚩 Page Banners
                </button>
                <button @click="cmsSubTab = 'policy_settings'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="cmsSubTab === 'policy_settings' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    ⚖️ Privacy & Terms
                </button>
            </div>

            <!-- 1.1 HERO SLIDERS MANAGER -->
            <div x-show="cmsSubTab === 'sliders'" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Hero Sliders Manager (CRUD)</h3>
                        <p class="text-xs text-slate-500">Manage cinematic background photo uploads, slide titles, subtitles, and CTA buttons.</p>
                    </div>
                    <button @click="openAddBanner()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">+ Add New Hero Slide</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="b in banners" :key="b.id">
                        <div class="relative bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm flex flex-col group hover:shadow-md transition-all">
                            <!-- Image and Overlay -->
                            <div class="h-44 relative bg-slate-100 overflow-hidden shrink-0">
                                <img :src="b.image || 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070'" alt="Slide preview" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent"></div>
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[9px] bg-amber-500 text-slate-950 font-black tracking-wider uppercase">Hero Slide</span>
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black tracking-wider uppercase" 
                                          :class="b.status === 'Active' ? 'bg-emerald-500 text-white' : 'bg-slate-500 text-white'"
                                          x-text="b.status || 'Active'"></span>
                                </div>
                                <div class="absolute bottom-4 left-4 right-4 text-left">
                                    <h4 class="font-extrabold text-white text-sm line-clamp-1" x-text="b.title || '(No Title)'"></h4>
                                    <p class="text-[10px] text-slate-200 line-clamp-1" x-text="b.subtitle || '(No Subtitle)'"></p>
                                </div>
                            </div>
                            
                            <!-- Slide CTAs and controls -->
                            <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                                <div class="space-y-2 text-[11px] text-slate-600 text-left">
                                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                        <span class="font-bold text-slate-400">Primary CTA:</span>
                                        <span class="font-extrabold text-slate-800" x-text="b.cta ? b.cta + ' (' + b.cta_link + ')' : 'None'"></span>
                                    </div>
                                    <div class="flex justify-between items-center pb-1">
                                        <span class="font-bold text-slate-400">Secondary CTA:</span>
                                        <span class="font-extrabold text-slate-800" x-text="b.secondary_cta ? b.secondary_cta + ' (' + b.secondary_cta_link + ')' : 'None'"></span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                    <button @click="openEditBanner(b)" class="flex-grow py-2 rounded-xl bg-slate-100 hover:bg-amber-500 hover:text-slate-950 text-slate-700 font-extrabold text-xs transition-colors flex items-center justify-center gap-1.5">
                                        ✏️ Edit Slide
                                    </button>
                                    <button @click="deleteBanner(b.id)" class="px-3 py-2 rounded-xl bg-red-50 hover:bg-red-600 text-red-500 hover:text-white font-extrabold text-xs transition-colors">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 1.2 ABOUT SECTION MANAGER WITH PHOTO UPLOAD -->
            <div x-show="cmsSubTab === 'about'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">About University Section Manager</h3>
                        <p class="text-xs text-slate-500">Edit homepage about paragraph, upload campus cover image, and update mission/vision.</p>
                    </div>
                    <button @click="saveAboutContent()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">Save About Changes</button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Campus Photo Upload Picker -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 flex flex-col sm:flex-row items-center gap-6">
                        <img :src="aboutContent.campusImage" alt="Campus Preview" class="w-32 h-24 rounded-2xl object-cover border border-slate-300">
                        <div class="space-y-2 flex-grow">
                            <label class="block font-extrabold uppercase text-slate-700">Campus Photo Upload</label>
                            <input type="file" id="aboutCampusFileInput" accept="image/*" @change="handlePhotoUpload($event, aboutContent, 'campusImage')" 
                                   class="block text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                            <span class="text-[10px] text-slate-500 block">PNG, JPG, WEBP supported. Preview updates automatically.</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-extrabold uppercase mb-1">Badge Tagline</label>
                        <input type="text" x-model="aboutContent.badge" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900">
                    </div>

                    <div>
                        <label class="block font-extrabold uppercase mb-1">Section Title</label>
                        <input type="text" x-model="aboutContent.title" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-extrabold text-slate-900">
                    </div>

                    <div>
                        <label class="block font-extrabold uppercase mb-1">Main Description Paragraph</label>
                        <textarea x-model="aboutContent.description" rows="3" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-semibold text-slate-900"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-extrabold uppercase mb-1">Mission Statement</label>
                            <input type="text" x-model="aboutContent.mission" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-semibold text-slate-900">
                        </div>
                        <div>
                            <label class="block font-extrabold uppercase mb-1">Vision Statement</label>
                            <input type="text" x-model="aboutContent.vision" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-semibold text-slate-900">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1.2.5 ACADEMIC JOURNEY BANNER (CTA SECTION) MANAGER -->
            <div x-show="cmsSubTab === 'cta'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Academic Journey Banner Manager</h3>
                        <p class="text-xs text-slate-500">Edit the high-impact call-to-action (CTA) section on the homepage and update its background image.</p>
                    </div>
                    <button @click="saveCtaContent()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">Save Banner Changes</button>
                </div>

                <div class="space-y-6 text-xs">
                    <!-- Live Mockup Preview -->
                    <div class="space-y-2">
                        <span class="block font-extrabold uppercase text-slate-500 tracking-wider">Live Mockup Preview</span>
                        <div class="relative rounded-3xl overflow-hidden bg-slate-950 text-white py-16 px-8 text-center border border-slate-800 shadow-2xl min-h-[300px] flex items-center justify-center">
                            <!-- Background Image Preview -->
                            <div class="absolute inset-0 z-0">
                                <img :src="aboutContent.ctaBackgroundImage || 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070'" 
                                     class="w-full h-full object-cover opacity-45" 
                                     alt="CTA Background Preview">
                            </div>
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/90 via-blue-950/70 to-slate-950/90 z-5"></div>
                            
                            <!-- Content -->
                            <div class="relative z-10 max-w-2xl mx-auto space-y-6">
                                <span class="px-4 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-400 text-[10px] font-extrabold uppercase tracking-widest inline-block"
                                      x-text="aboutContent.ctaBadge || 'Academic Cycle 2026 / 2027'"></span>
                                <h3 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight" 
                                    x-text="aboutContent.ctaTitle || 'Ready to Begin Your Academic Journey?'"></h3>
                                <p class="text-xs text-blue-100 max-w-lg mx-auto leading-relaxed" 
                                    x-text="aboutContent.ctaDescription || 'Take the first step towards securing your university degree. Submit your application online today in less than 10 minutes.'"></p>
                                <div class="flex justify-center gap-3 pt-2">
                                    <span class="gradient-btn-gold px-6 py-2.5 rounded-xl text-slate-950 font-extrabold text-[10px] shadow-md">Start Application Now &rarr;</span>
                                    <span class="px-4 py-2.5 rounded-xl bg-white/10 text-white font-bold text-[10px] border border-white/20">Track Existing Status</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload Picker -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 flex flex-col sm:flex-row items-center gap-6">
                        <img :src="aboutContent.ctaBackgroundImage || 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070'" 
                             alt="CTA Background Preview" 
                             class="w-32 h-20 rounded-2xl object-cover border border-slate-300 shadow-sm shrink-0">
                        <div class="space-y-2 flex-grow">
                            <label class="block font-extrabold uppercase text-slate-700">Banner Background Image</label>
                            <input type="file" id="ctaBackgroundFileInput" accept="image/*" @change="handlePhotoUpload($event, aboutContent, 'ctaBackgroundImage')" 
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                            <span class="text-[10px] text-slate-500 block font-medium">PNG, JPG, WEBP supported. Preview updates automatically.</span>
                        </div>
                    </div>

                    <!-- Input Fields -->
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block font-extrabold uppercase mb-1 text-slate-700">CTA Badge / Tagline</label>
                            <input type="text" x-model="aboutContent.ctaBadge" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                        </div>

                        <div>
                            <label class="block font-extrabold uppercase mb-1 text-slate-700">CTA Section Title</label>
                            <input type="text" x-model="aboutContent.ctaTitle" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-extrabold text-slate-900 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                        </div>

                        <div>
                            <label class="block font-extrabold uppercase mb-1 text-slate-700">CTA Description Paragraph</label>
                            <textarea x-model="aboutContent.ctaDescription" rows="4" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-semibold text-slate-900 focus:bg-white focus:border-blue-500 focus:outline-none transition-all"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1.3 FEATURED PROGRAMMES SHOWCASE MANAGER WITH PHOTO COVER -->
            <div x-show="cmsSubTab === 'programmes'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <span>🎓 Featured Programmes Showcase Manager</span>
                        </h3>
                        <p class="text-xs text-slate-500">Toggle featured status, upload cover photo images, and manage academic programmes.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="openAddProgrammeModal()" class="gradient-btn-gold px-5 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md cursor-pointer">+ Add New Programme</button>
                        <a href="{{ route('admin.programmes.index') }}" class="gradient-btn px-5 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md flex items-center gap-1">
                            <span>Full Catalog &rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <template x-for="p in featuredProgrammes" :key="p.id">
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-3">
                                <div class="relative group shrink-0">
                                    <img :src="p.image" alt="Programme photo" class="w-20 h-14 rounded-xl object-cover border border-slate-300 shadow-sm">
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-black text-amber-500 text-xs" x-text="p.code"></span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase"
                                              :class="p.featured ? 'bg-emerald-500/20 text-emerald-500 border border-emerald-500/30' : 'bg-slate-200 text-slate-500'"
                                              x-text="p.featured ? '✓ Featured' : 'Hidden'"></span>
                                    </div>
                                    <span class="font-extrabold text-slate-900 text-xs truncate max-w-[200px] block mt-0.5" x-text="p.name"></span>
                                    <span class="text-[10px] text-slate-500 block" x-text="p.duration_years ? p.duration_years + ' Years | TZS ' + Number(p.annual_fee).toLocaleString() : ''"></span>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-right w-full sm:w-auto flex flex-row sm:flex-col justify-between sm:justify-end items-center sm:items-end">
                                <button @click="toggleFeatured(p)" class="px-3.5 py-1.5 rounded-xl text-[10px] font-extrabold transition-all cursor-pointer"
                                        :class="p.featured ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-200 text-slate-600 hover:bg-slate-300'">
                                    <span x-text="p.featured ? '✓ Featured' : '+ Feature'"></span>
                                </button>
                                
                                <div class="relative">
                                    <label :for="'prog_photo_' + p.id" class="px-3 py-1.5 rounded-xl bg-blue-600/10 text-blue-600 hover:bg-blue-600 hover:text-white font-extrabold text-[10px] transition-all cursor-pointer inline-block">
                                        📷 Upload Cover
                                    </label>
                                    <input :id="'prog_photo_' + p.id" type="file" accept="image/*" @change="uploadProgrammePhoto($event, p)" class="hidden">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 1.35 NAVIGATION MEGA-MENU PROGRAMME CATEGORIES MANAGER -->
            <div x-show="cmsSubTab === 'prog_categories'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <span>📁 Navigation Mega-Menu Categories Manager (CRUD)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Configure academic catalog dropdown categories (e.g. Undergraduate, Postgraduate, Foundation Courses) displayed on the public navigation menu.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click.prevent.stop="openAddCategory()" class="gradient-btn-gold px-5 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md cursor-pointer">+ Add New Category</button>
                        <button type="button" @click.prevent.stop="saveProgrammeCategories()" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md cursor-pointer">Publish Mega-Menu</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs mb-6">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2">
                        <label class="block font-extrabold uppercase text-slate-900">Catalog Header Badge Text</label>
                        <input type="text" x-model="catalogHeader.title" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="Academic Catalog">
                    </div>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2">
                        <label class="block font-extrabold uppercase text-slate-900">Catalog Header Subtitle</label>
                        <input type="text" x-model="catalogHeader.subtitle" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="Explore Degrees & Diplomas">
                    </div>
                </div>

                <!-- INLINE ADD CATEGORY FORM -->
                <div x-show="showInlineAddCategory" x-cloak class="p-6 rounded-3xl bg-amber-500/10 border-2 border-amber-500/30 space-y-4 text-xs transition-all">
                    <div class="flex justify-between items-center border-b border-amber-500/20 pb-2">
                        <h4 class="font-black text-amber-500 text-sm uppercase tracking-wider">➕ Create New Academic Category</h4>
                        <button type="button" @click="showInlineAddCategory = false" class="text-slate-500 hover:text-white font-bold">✕</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-extrabold uppercase text-slate-900 mb-1">Code / Badge *</label>
                            <input type="text" x-model="newCategory.code" placeholder="e.g. DIP" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold uppercase">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-extrabold uppercase text-slate-900 mb-1">Category Title *</label>
                            <input type="text" x-model="newCategory.title" placeholder="e.g. Diploma Qualifications" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold">
                        </div>
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase text-slate-900 mb-1">Subtitle / Excerpt</label>
                        <input type="text" x-model="newCategory.subtitle" placeholder="e.g. 2-Year Ordinary Diploma Qualifications" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block font-extrabold uppercase text-slate-900 mb-1">Badge Color</label>
                            <select x-model="newCategory.color" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold">
                                <option value="blue">Blue (Undergraduate style)</option>
                                <option value="amber">Amber/Gold (Postgraduate style)</option>
                                <option value="emerald">Emerald/Green (Foundation style)</option>
                                <option value="purple">Purple Accent</option>
                                <option value="rose">Rose/Red Accent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-extrabold uppercase text-slate-900 mb-1">Initial Status</label>
                            <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-300 bg-white font-bold cursor-pointer">
                                <input type="checkbox" x-model="newCategory.is_active" class="w-4 h-4 rounded text-amber-500">
                                <span>Enabled on Navigation Dropdown</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showInlineAddCategory = false" class="px-5 py-2 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                        <button type="button" @click="addCategory()" class="gradient-btn-gold px-6 py-2 rounded-2xl text-slate-950 font-black text-xs shadow-md">Add & Save Category</button>
                    </div>
                </div>

                <!-- CATEGORIES LIST WITH INLINE AND MODAL EDIT SUPPORT -->
                <div class="space-y-4">
                    <template x-for="(cat, idx) in programmeCategories" :key="cat.id || idx">
                        <div class="rounded-2xl border border-slate-200 transition-all overflow-hidden">
                            <!-- NORMAL DISPLAY VIEW -->
                            <div x-show="editingCategoryIdx !== idx" class="p-5 bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
                                 :class="{ 'opacity-60 grayscale-[0.3]': !cat.is_active }">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-sm shrink-0 shadow-md"
                                         :class="{ 'bg-blue-600/20 text-blue-400': cat.color === 'blue', 'bg-amber-500/20 text-amber-400': cat.color === 'amber', 'bg-emerald-500/20 text-emerald-400': cat.color === 'emerald', 'bg-purple-600/20 text-purple-400': cat.color === 'purple', 'bg-rose-500/20 text-rose-400': cat.color === 'rose' || cat.color === 'red' }" x-text="cat.code"></div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-extrabold text-slate-900 text-sm" x-text="cat.title"></h4>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700" x-text="'Code: ' + cat.code"></span>
                                            
                                            <!-- Active / Disabled Badge -->
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase"
                                                  :class="cat.is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'">
                                                <span x-text="cat.is_active ? '✓ Enabled on Menu' : '✕ Disabled (Hidden)'"></span>
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="cat.subtitle"></p>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-2 shrink-0">
                                    <!-- Enable / Disable Toggle Action -->
                                    <button type="button" @click.prevent.stop="toggleCategoryActive(cat)" 
                                            class="px-3.5 py-1.5 rounded-xl font-extrabold text-[11px] transition-all flex items-center gap-1 cursor-pointer"
                                            :class="cat.is_active ? 'bg-emerald-500/10 text-emerald-500 hover:bg-emerald-600 hover:text-white' : 'bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-slate-950'">
                                        <span x-text="cat.is_active ? 'Disable' : 'Enable'"></span>
                                    </button>

                                    <!-- Edit Button -->
                                    <button type="button" @click.prevent.stop="openEditCategory(cat, idx)" class="px-3.5 py-1.5 rounded-xl bg-blue-600/10 text-blue-500 font-extrabold text-[11px] hover:bg-blue-600 hover:text-white transition-all cursor-pointer">
                                        Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" @click.prevent.stop="deleteCategory(idx)" class="px-3.5 py-1.5 rounded-xl bg-red-600/10 text-red-500 font-extrabold text-[11px] hover:bg-red-600 hover:text-white transition-all cursor-pointer">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <!-- INLINE EDITING CARD VIEW -->
                            <div x-show="editingCategoryIdx === idx" x-cloak class="p-6 bg-blue-950/40 border-2 border-blue-600/40 space-y-4 text-xs">
                                <div class="flex justify-between items-center border-b border-blue-500/20 pb-2">
                                    <h4 class="font-black text-blue-400 text-sm uppercase tracking-wider">✏️ Editing Academic Category</h4>
                                    <button type="button" @click="cancelInlineCategoryEdit()" class="text-slate-500 hover:text-white font-bold">✕</button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-extrabold uppercase text-slate-900 mb-1">Code / Badge *</label>
                                        <input type="text" x-model="inlineCategoryData.code" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold uppercase">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block font-extrabold uppercase text-slate-900 mb-1">Category Title *</label>
                                        <input type="text" x-model="inlineCategoryData.title" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold">
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-extrabold uppercase text-slate-900 mb-1">Subtitle / Excerpt</label>
                                    <input type="text" x-model="inlineCategoryData.subtitle" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                    <div>
                                        <label class="block font-extrabold uppercase text-slate-900 mb-1">Badge Color</label>
                                        <select x-model="inlineCategoryData.color" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold">
                                            <option value="blue">Blue (Undergraduate style)</option>
                                            <option value="amber">Amber/Gold (Postgraduate style)</option>
                                            <option value="emerald">Emerald/Green (Foundation style)</option>
                                            <option value="purple">Purple Accent</option>
                                            <option value="rose">Rose/Red Accent</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-extrabold uppercase text-slate-900 mb-1">Category Status</label>
                                        <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-300 bg-white font-bold cursor-pointer">
                                            <input type="checkbox" x-model="inlineCategoryData.is_active" class="w-4 h-4 rounded text-amber-500">
                                            <span>Enabled on Navigation Dropdown</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" @click="cancelInlineCategoryEdit()" class="px-5 py-2 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                                    <button type="button" @click="saveInlineCategoryEdit(idx)" class="gradient-btn px-6 py-2 rounded-2xl text-white font-black text-xs shadow-md">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 1.4 FOOTER & SOCIAL LINKS MANAGER -->
            <div x-show="cmsSubTab === 'footer'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Institutional Footer & Social Links</h3>
                        <p class="text-xs text-slate-500">Edit address, phone, email, copyright text, and social media URLs.</p>
                    </div>
                    <button @click="saveFooterContent()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">Save Footer Details</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Footer Tagline</label>
                        <input type="text" x-model="footerContent.tagline" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Copyright Text</label>
                        <input type="text" x-model="footerContent.copyright" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Phone Number</label>
                        <input type="text" x-model="footerContent.phone" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Email Address</label>
                        <input type="text" x-model="footerContent.email" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-extrabold uppercase mb-1">Physical Address</label>
                        <input type="text" x-model="footerContent.address" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Facebook URL</label>
                        <input type="text" x-model="footerContent.facebook" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold" placeholder="https://facebook.com/yourpage">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Twitter/X URL</label>
                        <input type="text" x-model="footerContent.twitter" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold" placeholder="https://twitter.com/yourhandle">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">LinkedIn URL</label>
                        <input type="text" x-model="footerContent.linkedin" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold" placeholder="https://linkedin.com/company/yourpage">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">YouTube URL</label>
                        <input type="text" x-model="footerContent.youtube" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold" placeholder="https://youtube.com/c/yourchannel">
                    </div>
                </div>
            </div>

            <!-- 1.5 LOGO & BRAND IDENTITY MANAGER WITH LOGO PHOTO UPLOAD -->
            <div x-show="cmsSubTab === 'logo'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <form id="logoManagementForm" @submit.prevent="saveBrandIdentity()" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                                <span>🏷️ Institutional Logos & Official Seal Management Desk</span>
                            </h3>
                            <p class="text-xs text-slate-500">Upload official institutional logos, seals, and registrar signature stamps used on student admission letters, printable forms, and public portals.</p>
                        </div>
                        <button type="submit" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md shrink-0">
                            Save Logos & Branding
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <!-- 1. STTC LOGO UPLOAD -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black uppercase text-slate-900 text-xs">1. STTC College Logo</span>
                                <span class="text-[10px] text-amber-500 font-extrabold">Printed on Admission Letters</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-slate-200 border border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="brandIdentity.sttcLogo">
                                        <img :src="brandIdentity.sttcLogo" alt="STTC Logo" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!brandIdentity.sttcLogo">
                                        <span class="text-2xl font-black text-amber-500">STTC</span>
                                    </template>
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <input type="file" name="sttc_logo" accept="image/*" @change="handlePhotoUpload($event, brandIdentity, 'sttcLogo')"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                    <span class="text-[10px] text-slate-500 block">PNG, JPG, WEBP or SVG up to 2MB.</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. OUT LOGO UPLOAD -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black uppercase text-slate-900 text-xs">2. Open University (OUT) Logo</span>
                                <span class="text-[10px] text-emerald-500 font-extrabold">Joint Partner Emblem</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-slate-200 border border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="brandIdentity.outLogo">
                                        <img :src="brandIdentity.outLogo" alt="OUT Logo" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!brandIdentity.outLogo">
                                        <span class="text-2xl font-black text-emerald-500">OUT</span>
                                    </template>
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <input type="file" name="out_logo" accept="image/*" @change="handlePhotoUpload($event, brandIdentity, 'outLogo')"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                                    <span class="text-[10px] text-slate-500 block">PNG, JPG, WEBP or SVG up to 2MB.</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. OFFICIAL ADMISSION SEAL -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black uppercase text-slate-900 text-xs">3. Official Admission Seal Stamp</span>
                                <span class="text-[10px] text-blue-500 font-extrabold">Watermark & Verification Stamp</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-slate-200 border border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="brandIdentity.officialSeal">
                                        <img :src="brandIdentity.officialSeal" alt="Official Seal" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!brandIdentity.officialSeal">
                                        <span class="text-2xl font-black text-blue-500">SEAL</span>
                                    </template>
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <input type="file" name="official_seal" accept="image/*" @change="handlePhotoUpload($event, brandIdentity, 'officialSeal')"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                                    <span class="text-[10px] text-slate-500 block">Transparent PNG recommended.</span>
                                </div>
                            </div>
                        </div>

                        <!-- 4. REGISTRAR SIGNATURE STAMP -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black uppercase text-slate-900 text-xs">4. Registrar Digital Signature Stamp</span>
                                <span class="text-[10px] text-purple-500 font-extrabold">Auto-Sign Admission Offer</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-slate-200 border border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="brandIdentity.registrarSignature">
                                        <img :src="brandIdentity.registrarSignature" alt="Registrar Signature" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!brandIdentity.registrarSignature">
                                        <span class="text-xl font-serif italic text-purple-500">Sign</span>
                                    </template>
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <input type="file" name="registrar_signature" accept="image/*" @change="handlePhotoUpload($event, brandIdentity, 'registrarSignature')"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                                    <span class="text-[10px] text-slate-500 block">Transparent PNG signature image.</span>
                                </div>
                            </div>
                        </div>

                        <!-- 5. SYSTEM MAIN PORTAL LOGO -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black uppercase text-slate-900 text-xs">5. System Main Portal Logo</span>
                                <span class="text-[10px] text-amber-500 font-extrabold">Public Header & Admin Sidebar Logo</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-slate-200 border border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="brandIdentity.systemLogo">
                                        <img :src="brandIdentity.systemLogo" alt="System Logo" class="w-full h-full object-contain p-1">
                                    </template>
                                    <template x-if="!brandIdentity.systemLogo">
                                        <span class="text-2xl font-black text-amber-500">LOGO</span>
                                    </template>
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <input type="file" name="system_logo" accept="image/*" @change="handlePhotoUpload($event, brandIdentity, 'systemLogo')"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                    <span class="text-[10px] text-slate-500 block">System emblem for headers.</span>
                                </div>
                            </div>
                        </div>

                        <!-- 6. LOGIN PAGE BACKGROUND IMAGE -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black uppercase text-slate-900 text-xs">6. Login Page Background Image</span>
                                <span class="text-[10px] text-blue-500 font-extrabold">Replaces or overlays animated backdrop</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-slate-200 border border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="brandIdentity.loginBackgroundImage">
                                        <img :src="brandIdentity.loginBackgroundImage" alt="Login Background" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!brandIdentity.loginBackgroundImage">
                                        <span class="text-[10px] font-bold text-slate-400">Default Gradient</span>
                                    </template>
                                </div>
                                <div class="space-y-1.5 flex-grow">
                                    <input type="file" name="login_background_image" accept="image/*" @change="handlePhotoUpload($event, brandIdentity, 'loginBackgroundImage')"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                                    <span class="text-[10px] text-slate-500 block">PNG, JPG, WEBP or SVG. Displays behind form.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER & SYSTEM DEVELOPER BRANDING -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-4 text-xs">
                        <h4 class="font-extrabold uppercase text-amber-500 text-xs border-b border-slate-200 pb-2">Institutional Heading & System Developer Copyright</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-extrabold uppercase text-slate-900 mb-1">University / System Name</label>
                                <input type="text" name="university_name" x-model="brandIdentity.universityName" 
                                       class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold text-slate-900">
                            </div>

                            <div>
                                <label class="block font-extrabold uppercase text-slate-900 mb-1">Footer Copyright Line</label>
                                <input type="text" name="footer_copyright" x-model="brandIdentity.footerCopyright" 
                                       class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold text-slate-900"
                                       placeholder="© 2026 SUPA / OUT University. All rights reserved.">
                            </div>

                            <div>
                                <label class="block font-extrabold uppercase text-slate-900 mb-1">Developer Company Name</label>
                                <input type="text" name="developer_name" x-model="brandIdentity.developerName" 
                                       class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold text-slate-900"
                                       placeholder="Reliance Solutions & Technology">
                            </div>

                            <div>
                                <label class="block font-extrabold uppercase text-slate-900 mb-1">Developer Website Link (URL)</label>
                                <input type="url" name="developer_url" x-model="brandIdentity.developerUrl" 
                                       class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold text-blue-500"
                                       placeholder="http://www.reliancesolutions.co.tz">
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 font-semibold text-[11px]">
                            💡 Preview of Developer Credit Link: 
                            <span>Developed by </span>
                            <a :href="brandIdentity.developerUrl" target="_blank" class="font-extrabold underline" x-text="brandIdentity.developerName + ' (' + brandIdentity.developerUrl + ')'"></a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 1.10 PAGE BANNER BACKGROUND MANAGER -->
            <div x-show="cmsSubTab === 'banners'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-base">🚩 Sub-Page Banners Background Manager</h3>
                    <p class="text-xs text-slate-500">Upload high-resolution background photos for each public sub-page's top header banner. If no image is uploaded, the page will fall back to the standard dark slate/blue gradient.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="p in [
                        { key: 'programmes', label: 'Programmes Catalog', path: '/programmes' },
                        { key: 'requirements', label: 'Admission Requirements', path: '/admission-requirements' },
                        { key: 'track', label: 'Track Application Status', path: '/track-application' },
                        { key: 'news', label: 'News & Announcements', path: '/news' },
                        { key: 'contact', label: 'Contact Admissions', path: '/contact' },
                        { key: 'careers', label: 'Careers Portal', path: '/careers' },
                        { key: 'downloads', label: 'Download Hub', path: '/downloads' },
                        { key: 'faqs', label: 'FAQs Help Center', path: '/faqs' }
                    ]" :key="p.key">
                        <div class="bg-slate-50 p-5 rounded-3xl border border-slate-200 flex flex-col justify-between space-y-4 hover:shadow-lg transition-all">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-black text-slate-900 text-xs" x-text="p.label"></h4>
                                        <span class="text-[10px] text-slate-500 font-bold" x-text="p.path"></span>
                                    </div>
                                </div>

                                <!-- Banner Preview Container -->
                                <div class="relative h-28 rounded-2xl overflow-hidden border border-slate-200 flex items-center justify-center">
                                    <template x-if="pageBanners[p.key]">
                                        <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat w-full h-full" :style="'background-image: linear-gradient(to right, rgba(2, 6, 23, 0.85), rgba(30, 58, 138, 0.75)), url(' + pageBanners[p.key] + ')'"></div>
                                    </template>
                                    <template x-if="!pageBanners[p.key]">
                                        <div class="absolute inset-0 z-0 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 w-full h-full"></div>
                                    </template>
                                    
                                    <div class="relative z-10 text-center px-3 py-2">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase"
                                              :class="pageBanners[p.key] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-500/20 text-slate-400 border border-slate-600/30'">
                                            <span x-text="pageBanners[p.key] ? 'Custom Background' : 'Default Gradient'"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Upload Input -->
                                <div class="space-y-1 text-xs">
                                    <label class="block text-[10px] font-extrabold uppercase text-slate-500">Change Background</label>
                                    <input type="file" :id="'banner_input_' + p.key" accept="image/*" @change="savePageBanner(p.key, 'banner_input_' + p.key)"
                                           class="block w-full text-[10px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200 flex justify-end">
                                <button type="button" x-show="pageBanners[p.key]" @click="removePageBanner(p.key)"
                                        class="w-full text-center px-3 py-1.5 rounded-xl bg-red-600/10 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-[10px] transition-all cursor-pointer">
                                    Revert to Default
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 1.11 PRIVACY & TERMS POLICY SETTINGS -->
            <div x-show="cmsSubTab === 'policy_settings'" x-cloak class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">⚖️ Privacy Policy & Terms Management Desk</h3>
                        <p class="text-xs text-slate-500">Edit the official Privacy Policy and Terms of Admission content. Standard HTML formatting is supported.</p>
                    </div>
                    <button type="button" @click="savePolicyContent()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md shrink-0">
                        Publish Policy Changes
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <!-- PRIVACY POLICY EDITOR -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="font-black uppercase text-slate-900 text-xs">Privacy Policy Document</span>
                            <a href="{{ route('public.privacy') }}" target="_blank" class="text-[10px] text-blue-600 font-extrabold hover:underline">View Live Page &nearr;</a>
                        </div>
                        <div class="space-y-2">
                            <label class="block font-bold text-slate-700">HTML Content</label>
                            <textarea x-model="policies.privacy" rows="18" 
                                      class="w-full p-4 rounded-2xl border border-slate-300 bg-slate-50 font-mono text-[11px] text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-all"
                                      placeholder="Enter HTML tags (e.g. <h2>, <p>)..."></textarea>
                        </div>
                    </div>

                    <!-- TERMS & CONDITIONS EDITOR -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="font-black uppercase text-slate-900 text-xs">Terms & Conditions of Admission</span>
                            <a href="{{ route('public.terms') }}" target="_blank" class="text-[10px] text-blue-600 font-extrabold hover:underline">View Live Page &nearr;</a>
                        </div>
                        <div class="space-y-2">
                            <label class="block font-bold text-slate-700">HTML Content</label>
                            <textarea x-model="policies.terms" rows="18" 
                                      class="w-full p-4 rounded-2xl border border-slate-300 bg-slate-50 font-mono text-[11px] text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-all"
                                      placeholder="Enter HTML tags (e.g. <h2>, <p>)..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1.6 NEWS & ANNOUNCEMENTS MANAGER -->
            <div x-show="cmsSubTab === 'news'" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">News, Circulars & Announcements Manager (CRUD)</h3>
                        <p class="text-xs text-slate-500">Publish, edit, feature, and delete news articles for the public admission portal.</p>
                    </div>
                    <button @click="showNewsModal = true" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">+ Publish News Article</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="n in newsList" :key="n.id">
                        <div class="bg-slate-50 p-5 rounded-3xl border border-slate-200 flex flex-col justify-between space-y-4 hover:shadow-lg transition-all">
                            <div class="space-y-3">
                                <div class="relative h-44 rounded-2xl overflow-hidden bg-white border border-slate-200">
                                    <img :src="n.image || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=800'" alt="News Cover" class="w-full h-full object-cover">
                                    <div class="absolute top-3 left-3 flex gap-2">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold shadow-md"
                                              :class="n.is_featured ? 'bg-amber-500 text-slate-950' : 'bg-white/80 text-white'">
                                            <span x-text="n.is_featured ? '⭐ Featured' : 'Standard'"></span>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[11px] font-extrabold text-amber-500" x-text="n.published_at"></span>
                                    <h4 class="font-black text-slate-900 text-sm line-clamp-2 mt-1" x-text="n.title"></h4>
                                    <p class="text-xs text-slate-500 line-clamp-3 mt-1" x-text="n.summary"></p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-2">
                                <button @click="toggleFeaturedNews(n)" 
                                        class="px-3 py-1.5 rounded-xl text-[11px] font-extrabold transition-all"
                                        :class="n.is_featured ? 'bg-amber-500/20 text-amber-500 hover:bg-amber-500/30' : 'bg-slate-200 text-slate-600 hover:bg-amber-500 hover:text-slate-950'">
                                    <span x-text="n.is_featured ? 'Unfeature' : 'Feature'"></span>
                                </button>
                                <div class="flex items-center gap-2">
                                    <button @click="openEditNews(n)" class="px-3 py-1.5 rounded-xl bg-blue-600/10 text-blue-500 font-extrabold text-[11px] hover:bg-blue-600 hover:text-white transition-all">Edit</button>
                                    <button @click="selectedNews = n; showDeleteNewsModal = true" class="px-3 py-1.5 rounded-xl bg-red-600/10 text-red-500 font-extrabold text-[11px] hover:bg-red-600 hover:text-white transition-all">Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 1.7 CONTACT PAGE INFO SETTINGS -->
            <div x-show="cmsSubTab === 'contact_settings'" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Contact Page Information & Support Channels</h3>
                        <p class="text-xs text-slate-500">Configure public support emails, phone numbers, WhatsApp, physical address, working hours, and map link.</p>
                    </div>
                    <button @click="saveContactSettings()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">Save Contact Info</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-4">
                        <h4 class="font-black uppercase text-amber-500">📞 Support Phones & Helpline</h4>
                        <div>
                            <label class="block font-bold mb-1">Telephone Support Phone Numbers</label>
                            <input type="text" x-model="contactSettings.phone" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="+255 22 266 8820 / +255 754 123 456">
                        </div>
                        <div>
                            <label class="block font-bold mb-1">WhatsApp Helpline Number</label>
                            <input type="text" x-model="contactSettings.whatsapp" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="+255754123456">
                        </div>
                        <div>
                            <label class="block font-bold mb-1">Official Admissions Email Address</label>
                            <input type="email" x-model="contactSettings.email" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="admissions@supa.ac.tz">
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-4">
                        <h4 class="font-black uppercase text-amber-500">📍 Physical Address & Operating Hours</h4>
                        <div>
                            <label class="block font-bold mb-1">Main Campus Physical Address</label>
                            <textarea x-model="contactSettings.address" rows="2" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="Singida Campus, Main Academic Building, Singida, Tanzania"></textarea>
                        </div>
                        <div>
                            <label class="block font-bold mb-1">Admissions Desk Working Hours</label>
                            <input type="text" x-model="contactSettings.hours" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="Monday - Friday: 08:00 AM - 05:00 PM">
                        </div>
                        <div>
                            <label class="block font-bold mb-1">Google Maps / Location Link</label>
                            <input type="text" x-model="contactSettings.map_url" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold" placeholder="https://maps.google.com/?q=Singida">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- CONTACT INQUIRIES DESK TAB -->
        <div x-show="activeTab === 'contact'" x-cloak class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Public Contact Inquiries & Support Tickets</h3>
                        <p class="text-xs text-slate-500">Review, process, and manage support messages submitted via the public contact page.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="contactFilter = 'ALL'" class="px-4 py-2 rounded-2xl text-xs font-extrabold" :class="contactFilter === 'ALL' ? 'bg-amber-500 text-slate-950 shadow-md' : 'bg-slate-100 text-slate-500'">All (<span x-text="contactMessages.length"></span>)</button>
                        <button @click="contactFilter = 'UNREAD'" class="px-4 py-2 rounded-2xl text-xs font-extrabold" :class="contactFilter === 'UNREAD' ? 'bg-red-500 text-white shadow-md' : 'bg-slate-100 text-slate-500'">Unread (<span x-text="contactMessages.filter(c => !c.is_read).length"></span>)</button>
                        <button @click="contactFilter = 'READ'" class="px-4 py-2 rounded-2xl text-xs font-extrabold" :class="contactFilter === 'READ' ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-100 text-slate-500'">Read (<span x-text="contactMessages.filter(c => c.is_read).length"></span>)</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-black uppercase text-slate-500">
                                <th class="py-3 px-4">Sender</th>
                                <th class="py-3 px-4">Subject</th>
                                <th class="py-3 px-4">Message Excerpt</th>
                                <th class="py-3 px-4">Date Sent</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <template x-for="c in contactMessages.filter(item => contactFilter === 'ALL' || (contactFilter === 'UNREAD' && !item.is_read) || (contactFilter === 'READ' && item.is_read))" :key="c.id">
                                <tr class="hover:bg-slate-50 transition-colors" :class="!c.is_read ? 'font-bold bg-amber-500/5' : ''">
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900" x-text="c.name"></div>
                                        <div class="text-[11px] text-slate-500" x-text="c.email + (c.phone ? ' • ' + c.phone : '')"></div>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-800" x-text="c.subject"></td>
                                    <td class="py-4 px-4 text-slate-500 max-w-xs truncate" x-text="c.message"></td>
                                    <td class="py-4 px-4 text-[11px] text-slate-500 whitespace-nowrap" x-text="c.date"></td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold"
                                              :class="c.is_read ? 'bg-emerald-500/20 text-emerald-500' : 'bg-red-500/20 text-red-500 animate-pulse'">
                                            <span x-text="c.is_read ? 'Read' : 'UNREAD'"></span>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right space-x-2 whitespace-nowrap">
                                        <button @click="selectedContact = c; showContactModal = true; if(!c.is_read) toggleReadContact(c);" class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-extrabold text-[11px]">View</button>
                                        <button @click="toggleReadContact(c)" class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-700 font-extrabold text-[11px]" x-text="c.is_read ? 'Mark Unread' : 'Mark Read'"></button>
                                        <button @click="deleteContact(c)" class="px-3 py-1.5 rounded-xl bg-red-600/10 text-red-500 font-extrabold text-[11px] hover:bg-red-600 hover:text-white">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. MEDIA LIBRARY TAB WITH PHOTO UPLOADER & PREVIEW GRID -->
        <div x-show="activeTab === 'media'" x-cloak class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Media Library Assets & Photo Uploads (CRUD)</h3>
                        <p class="text-xs text-slate-500">Upload and manage prospectuses, forms, and image files.</p>
                    </div>
                    <button @click="showMediaModal = true" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">+ Upload Asset</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <template x-for="m in mediaAssets" :key="m.id">
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 text-center space-y-3 relative group">
                            <template x-if="m.preview">
                                <img :src="m.preview" alt="Media preview" class="w-full h-24 rounded-xl object-cover border border-slate-300">
                            </template>
                            <template x-if="!m.preview">
                                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center mx-auto text-xl font-bold" x-text="m.type === 'PDF' ? '📄' : '📷'"></div>
                            </template>

                            <div>
                                <span class="text-xs font-extrabold text-slate-900 block truncate" x-text="m.name"></span>
                                <span class="text-[10px] text-slate-500 font-bold" x-text="m.size + ' • ' + m.category"></span>
                            </div>
                            <button @click="deleteMedia(m.id)" class="w-full py-1.5 rounded-xl bg-red-600/10 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-[10px] transition-all">Delete</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 3. USERS & ROLES TAB -->
        <div x-show="activeTab === 'users'" x-cloak class="space-y-6">
            
            <!-- Users Sub-Tab Navigation Pills -->
            <div class="flex flex-wrap gap-3 p-1.5 rounded-2xl bg-slate-100 text-xs font-bold w-fit">
                <button @click="userSubTab = 'users'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="userSubTab === 'users' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    👥 Users Directory
                </button>
                <button @click="userSubTab = 'roles'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="userSubTab === 'roles' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    🔑 Role Permissions Manager
                </button>
                <button @click="userSubTab = 'permissions'" 
                        class="px-4 py-2 rounded-xl transition-all"
                        :class="userSubTab === 'permissions' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'">
                    🛡️ System Permissions Catalog
                </button>
            </div>

            <!-- 3.1 USERS DIRECTORY SUB-TAB -->
            <div x-show="userSubTab === 'users'" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Users Directory</h3>
                        <p class="text-xs text-slate-500">Add, view, edit, and assign staff roles & access levels.</p>
                    </div>
                    <button @click="showUserModal = true" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md hover:scale-105 transition-transform flex items-center gap-2">
                        <span>+ Add New User Account</span>
                    </button>
                </div>

                <!-- Search Bar & Filters Header -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="w-full sm:w-80">
                        <input type="text" x-model="userSearch" placeholder="Search user name, email or role..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">
                        <select x-model="userRoleFilter" class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold outline-none">
                            <option value="ALL">All System Roles</option>
                            <option value="SUPER_ADMIN">SUPER_ADMIN</option>
                            <option value="REGISTRAR">REGISTRAR</option>
                            <option value="ADMISSION_OFFICER">ADMISSION_OFFICER</option>
                            <option value="FINANCE_OFFICER">FINANCE_OFFICER</option>
                            <option value="APPLICANT">APPLICANT</option>
                        </select>

                        <select x-model="userStatusFilter" class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold outline-none">
                            <option value="ALL">All Statuses</option>
                            <option value="Active">Active</option>
                            <option value="Deactivated">Deactivated</option>
                        </select>

                        <span class="text-xs font-extrabold text-slate-500">Total: <strong class="text-slate-900" x-text="filteredUsers().length"></strong></span>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                                <th class="py-3 px-4">User Name</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions (CRUD & Edit)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold">
                            <template x-for="u in filteredUsers()" :key="u.id">
                                <tr class="hover:bg-slate-50 transition-colors" :class="{'bg-amber-500/5 border-l-4 border-amber-500': inlineEditingUserId === u.id}">
                                    
                                    <!-- USER NAME CELL -->
                                    <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                        <template x-if="inlineEditingUserId === u.id">
                                            <input type="text" x-model="inlineEditUserData.name" class="w-full min-w-[150px] p-2 rounded-xl border border-amber-500 bg-white text-xs font-bold outline-none">
                                        </template>
                                        <template x-if="inlineEditingUserId !== u.id">
                                            <span x-text="u.name"></span>
                                        </template>
                                    </td>

                                    <!-- EMAIL CELL -->
                                    <td class="py-3.5 px-4 text-slate-500">
                                        <template x-if="inlineEditingUserId === u.id">
                                            <input type="email" x-model="inlineEditUserData.email" class="w-full min-w-[180px] p-2 rounded-xl border border-amber-500 bg-white text-xs font-semibold outline-none">
                                        </template>
                                        <template x-if="inlineEditingUserId !== u.id">
                                            <span x-text="u.email"></span>
                                        </template>
                                    </td>

                                    <!-- ROLE CELL -->
                                    <td class="py-3.5 px-4">
                                        <template x-if="inlineEditingUserId === u.id">
                                            <select x-model="inlineEditUserData.role" class="p-2 rounded-xl border border-amber-500 bg-white text-xs font-extrabold outline-none">
                                                <option value="SUPER_ADMIN">SUPER_ADMIN</option>
                                                <option value="REGISTRAR">REGISTRAR</option>
                                                <option value="ADMISSION_OFFICER">ADMISSION_OFFICER</option>
                                                <option value="FINANCE_OFFICER">FINANCE_OFFICER</option>
                                                <option value="APPLICANT">APPLICANT</option>
                                            </select>
                                        </template>
                                        <template x-if="inlineEditingUserId !== u.id">
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="r in u.roles" :key="r">
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider animate-pulse-subtle"
                                                          :class="{ 
                                                              'bg-amber-500/20 text-amber-500 border border-amber-500/30': r === 'SUPER_ADMIN' || r === 'SUPER_ADMINISTRATOR', 
                                                              'bg-purple-500/20 text-purple-400 border border-purple-500/30': r === 'REGISTRAR', 
                                                              'bg-blue-500/20 text-blue-400 border border-blue-500/30': r === 'ADMISSION_OFFICER', 
                                                              'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': r === 'FINANCE_OFFICER', 
                                                              'bg-slate-500/20 text-slate-500 border border-slate-500/30': r === 'APPLICANT' || r === 'USER' 
                                                          }"
                                                          x-text="r"></span>
                                                </template>
                                                <template x-if="!u.roles || u.roles.length === 0">
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-slate-500/20 text-slate-500 border border-slate-500/30 uppercase tracking-wider animate-pulse-subtle" x-text="u.role"></span>
                                                </template>
                                            </div>
                                        </template>
                                    </td>

                                    <!-- STATUS CELL -->
                                    <td class="py-3.5 px-4">
                                        <template x-if="inlineEditingUserId === u.id">
                                            <select x-model="inlineEditUserData.status" class="p-2 rounded-xl border border-amber-500 bg-white text-xs font-extrabold outline-none">
                                                <option value="Active">Active</option>
                                                <option value="Deactivated">Deactivated</option>
                                            </select>
                                        </template>
                                        <template x-if="inlineEditingUserId !== u.id">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase"
                                                  :class="u.status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                                  x-text="u.status || 'Active'"></span>
                                        </template>
                                    </td>

                                    <!-- ACTIONS CELL -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <template x-if="inlineEditingUserId === u.id">
                                            <div class="flex justify-end space-x-1">
                                                <button @click="saveInlineUserEdit()" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-extrabold text-[10px] hover:bg-emerald-700 transition-all flex items-center gap-1">
                                                    <span>✓ Save</span>
                                                </button>
                                                <button @click="cancelInlineUserEdit()" class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-700 font-extrabold text-[10px]">
                                                    <span>✕ Cancel</span>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="inlineEditingUserId !== u.id">
                                            <div class="flex items-center justify-end space-x-1.5">
                                                <button @click="startInlineUserEdit(u)" title="Quick Edit Inline" class="px-2.5 py-1.5 rounded-xl bg-amber-500/10 text-amber-600 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] transition-all">
                                                    ⚡ Quick Edit
                                                </button>
                                                <button @click="openEditUser(u)" title="Edit Account Modal" class="px-3 py-1.5 rounded-xl bg-blue-600/10 text-blue-600 hover:bg-blue-600 hover:text-white font-extrabold text-[10px] transition-all">
                                                    Edit Account
                                                </button>
                                                <button @click="toggleUserStatus(u)" :class="u.status === 'Active' ? 'bg-amber-500/10 text-amber-600 hover:bg-amber-500 hover:text-slate-950' : 'bg-emerald-600/10 text-emerald-600 hover:bg-emerald-600 hover:text-white'" class="px-3 py-1.5 rounded-xl font-extrabold text-[10px] transition-all" x-text="u.status === 'Active' ? 'Deactivate' : 'Reactivate'"></button>
                                                <button @click="confirmDeleteUser(u)" title="Delete Account" class="px-2.5 py-1.5 rounded-xl bg-red-600/10 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-[10px] transition-all">
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
            </div> <!-- End of Users Directory Sub-Tab -->

            <!-- 3.2 ROLE PERMISSIONS MANAGER SUB-TAB -->
            <div x-show="userSubTab === 'roles'" class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-cloak>
                <!-- Role List Column -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-6 lg:col-span-1">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <h4 class="font-extrabold text-slate-900 text-sm">System Roles</h4>
                        <button @click="showAddRoleModal = true" class="text-[11px] font-black text-amber-500 hover:underline">+ Add Role</button>
                    </div>
                    
                    <div class="space-y-2">
                        <template x-for="r in rolesList" :key="r.id">
                            <div @click="selectRole(r)" 
                                 class="p-4 rounded-2xl border transition-all cursor-pointer text-left"
                                 :class="selectedRoleId === r.id ? 'bg-amber-500/10 border-amber-500 shadow-sm' : 'bg-slate-50 border-slate-200 hover:bg-slate-100'">
                                <div class="flex justify-between items-center">
                                    <span class="font-extrabold text-slate-900 text-xs uppercase" x-text="r.display_name"></span>
                                    <button x-show="!['super_admin', 'registrar', 'admission_officer', 'finance_officer', 'applicant'].includes(r.name)" 
                                            @click.stop="confirmDeleteRole(r)" 
                                            class="text-red-500 hover:text-red-700 text-xs">🗑️</button>
                                </div>
                                <span class="text-[10px] text-slate-500 mt-1 block" x-text="r.description || 'No description provided.'"></span>
                                <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-extrabold text-[8px] mt-2 inline-block" x-text="(r.permissions ? r.permissions.length : 0) + ' permissions'"></span>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Permissions checklist for selected role -->
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6 lg:col-span-2 text-left">
                    <template x-if="selectedRole">
                        <div class="space-y-6">
                            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm">Permissions for: <span class="text-amber-500 uppercase" x-text="selectedRole.display_name"></span></h4>
                                    <p class="text-[10px] text-slate-500" x-text="selectedRole.description"></p>
                                </div>
                                <button @click="saveRolePermissions()" class="gradient-btn-gold px-5 py-2 rounded-xl text-slate-950 font-black text-xs shadow-md">
                                    Save Permissions
                                </button>
                            </div>
                            
                            <!-- Permissions Grouped by Category -->
                            <div class="space-y-6 max-h-[500px] overflow-y-auto pr-2">
                                <template x-for="group in Object.keys(groupedPermissions)" :key="group">
                                    <div class="space-y-2 border border-slate-100 p-4 rounded-2xl bg-slate-50">
                                        <h5 class="text-[10px] font-black uppercase text-slate-500 tracking-wider" x-text="group"></h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <template x-for="p in groupedPermissions[group]" :key="p.id">
                                                <label class="flex items-start gap-3 p-2 rounded-xl hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer text-xs transition-all">
                                                    <input type="checkbox" 
                                                           :value="p.id" 
                                                           x-model="selectedRolePermissionIds" 
                                                           class="rounded text-amber-500 mt-0.5">
                                                    <div>
                                                        <span class="font-bold text-slate-800" x-text="p.display_name"></span>
                                                        <span class="block text-[9px] text-slate-400" x-text="p.name"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!selectedRole">
                        <div class="h-64 flex flex-col items-center justify-center text-slate-400 space-y-2">
                            <span class="text-3xl">🔑</span>
                            <span class="text-xs font-bold">Select a role from the left pane to edit its permission mappings.</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 3.3 SYSTEM PERMISSIONS CATALOG SUB-TAB -->
            <div x-show="userSubTab === 'permissions'" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6" x-cloak>
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-base">System Permissions Catalog</h3>
                    <p class="text-xs text-slate-500">Read-only reference list of all system permissions built into the application codebase.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                    <template x-for="group in Object.keys(groupedPermissions)" :key="group">
                        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-200 pb-2" x-text="group + ' Group'"></h4>
                            <div class="space-y-3">
                                <template x-for="p in groupedPermissions[group]" :key="p.id">
                                    <div class="flex items-start gap-2">
                                        <span class="text-amber-500">🛡️</span>
                                        <div>
                                            <span class="font-bold text-xs text-slate-800 block" x-text="p.display_name"></span>
                                            <code class="text-[9px] bg-slate-200 px-1 py-0.5 rounded text-indigo-700 font-mono" x-text="p.name"></code>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 4. REPORTS & ANALYTICS TAB -->
        @if(auth()->user()->hasPermissionTo('view_reports'))
        <div x-show="activeTab === 'reports'" x-cloak class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Executive Reports Exporter</h3>
                        <p class="text-xs text-slate-500">Download formatted admission logs and CSV financial reports.</p>
                    </div>
                </div>

                <!-- Interactive Premium Filter Panel -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-150 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-700 flex items-center gap-1.5 uppercase tracking-wider">
                            ⚙️ Report Filters
                        </span>
                        <button type="button" @click="resetReportFilters()" class="text-[10px] text-blue-600 hover:text-blue-800 font-extrabold transition-colors">
                            Reset All Filters
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                        <!-- Year Selector -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-500 uppercase mb-1">Academic Year</label>
                            <select x-model="reportFilter.year" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                                <option value="">All Years</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                            </select>
                        </div>
                        <!-- Month Selector -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-500 uppercase mb-1">Created Month</label>
                            <select x-model="reportFilter.month" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                                <option value="">All Months</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <!-- Start Date -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-500 uppercase mb-1">Start Date</label>
                            <input type="date" x-model="reportFilter.start_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        <!-- End Date -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-500 uppercase mb-1">End Date</label>
                            <input type="date" x-model="reportFilter.end_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                        <!-- Status Selector (Admissions & Payments) -->
                        <div>
                            <label class="block text-[9px] font-extrabold text-slate-500 uppercase mb-1">Status Classification</label>
                            <select x-model="reportFilter.status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                                <option value="">All (Default)</option>
                                <optgroup label="Admissions / Applications">
                                    <option value="pending">Pending Admissions (All)</option>
                                    <option value="active">Active Admissions (Approved)</option>
                                    <option value="Rejected">Rejected Admissions</option>
                                    <option value="Draft">Draft Mode</option>
                                </optgroup>
                                <optgroup label="Payments Status">
                                    <option value="paid">Paid & Verified Payments</option>
                                    <option value="pending">Pending Payments</option>
                                    <option value="rejected">Rejected Payments</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 font-semibold italic">
                        💡 Note: Filter conditions apply instantly to all download and export links below.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <h4 class="font-extrabold text-slate-900 text-sm">Applications Report</h4>
                        <p class="text-xs text-slate-500">Complete listing of student applicants and status.</p>
                        <div class="space-y-2">
                            @if(auth()->user()->hasPermissionTo('download_reports'))
                            <a :href="getReportUrl('applications', 'pdf')" target="_blank" class="w-full bg-blue-600 hover:bg-blue-700 text-white block text-center py-2.5 rounded-2xl font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                                📄 Download PDF Report (With Logo)
                            </a>
                            <a :href="getReportUrl('applications', 'csv')" target="_blank" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-800 block text-center py-2 rounded-2xl font-bold text-xs transition-all">
                                Export Applications CSV &rarr;
                            </a>
                            @else
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                                <span class="text-xs font-bold text-slate-400 block">🔒 Downloads Disabled</span>
                                <span class="text-[9px] text-slate-400 mt-1 block">Requires download permission</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <h4 class="font-extrabold text-slate-900 text-sm">Payments & Revenue</h4>
                        <p class="text-xs text-slate-500">Control numbers and verified fee receipts.</p>
                        <div class="space-y-2">
                            @if(auth()->user()->hasPermissionTo('download_reports'))
                            <a :href="getReportUrl('payments', 'pdf')" target="_blank" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 block text-center py-2.5 rounded-2xl font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                                📄 Download PDF Report (With Logo)
                            </a>
                            <a :href="getReportUrl('payments', 'csv')" target="_blank" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-800 block text-center py-2 rounded-2xl font-bold text-xs transition-all">
                                Export Revenue CSV &rarr;
                            </a>
                            @else
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                                <span class="text-xs font-bold text-slate-400 block">🔒 Downloads Disabled</span>
                                <span class="text-[9px] text-slate-400 mt-1 block">Requires download permission</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <h4 class="font-extrabold text-slate-900 text-sm">Admitted Students List</h4>
                        <p class="text-xs text-slate-500">Approved candidates with issued admission numbers.</p>
                        <div class="space-y-2">
                            @if(auth()->user()->hasPermissionTo('download_reports'))
                            <a :href="getReportUrl('admitted', 'pdf')" target="_blank" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white block text-center py-2.5 rounded-2xl font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                                📄 Download PDF Report (With Logo)
                            </a>
                            <a :href="getReportUrl('admitted', 'csv')" target="_blank" class="w-full bg-slate-200 hover:bg-slate-300 text-slate-800 block text-center py-2 rounded-2xl font-bold text-xs transition-all">
                                Export Admitted List &rarr;
                            </a>
                            @else
                            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                                <span class="text-xs font-bold text-slate-400 block">🔒 Downloads Disabled</span>
                                <span class="text-[9px] text-slate-400 mt-1 block">Requires download permission</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- 5. COMMUNICATION LOGS TAB -->
        <div x-show="activeTab === 'comm'" x-cloak class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Communication & SMS Gateway (CRUD)</h3>
                        <p class="text-xs text-slate-500">Dispatch SMS / Email notifications to applicants.</p>
                    </div>
                    <button @click="showCommModal = true" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">+ Dispatch Alert</button>
                </div>

                <div class="space-y-3">
                    <template x-for="c in commLogs" :key="c.id">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 flex justify-between items-center text-xs">
                            <div>
                                <span class="font-extrabold text-slate-900 block" x-text="c.title + ' • ' + c.channel"></span>
                                <span class="text-[10px] text-slate-500" x-text="'Recipient: ' + c.recipient"></span>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold" x-text="c.status"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 6. SYSTEM SETTINGS TAB -->
        <div x-show="activeTab === 'settings'" x-cloak class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3">Admission Portal System Settings</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-2">Active Academic Year</label>
                        <input type="text" x-model="settings.academicYear" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-2">Application Processing Fee (TZS)</label>
                        <input type="number" x-model="settings.applicationFee" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900">
                    </div>
                </div>

                <div class="flex items-center space-x-3 pt-2">
                    <input type="checkbox" id="autoCatCheck" x-model="settings.autoCalculatedCategories" class="rounded text-amber-500">
                    <label for="autoCatCheck" class="text-xs font-bold text-slate-700">Enable Automated Category Rule Evaluation (GPA &ge; 3.0 / Points &ge; 5)</label>
                </div>

                <!-- Applicant Admissions Configuration -->
                <div class="border-t border-slate-100 pt-6 space-y-4">
                    <h4 class="font-extrabold text-slate-900 text-sm">Applicant Admissions Settings</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                        <div class="flex items-center space-x-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <input type="checkbox" id="loginReqCheck" x-model="settings.applicantLoginRequired" class="rounded text-amber-500">
                            <div>
                                <label for="loginReqCheck" class="block font-bold text-slate-900">Enable Login Before Application</label>
                                <span class="text-[10px] text-slate-500 block">If disabled, guests can submit applications and claim/convert accounts later.</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <input type="checkbox" id="emailVerifyCheck" x-model="settings.emailVerificationRequired" class="rounded text-amber-500">
                            <div>
                                <label for="emailVerifyCheck" class="block font-bold text-slate-900">Require Email Verification (OTP)</label>
                                <span class="text-[10px] text-slate-500 block">Enforce simulated OTP validation during account registration.</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <input type="checkbox" id="autoActivateCheck" x-model="settings.applicantAutoActivate" class="rounded text-amber-500">
                            <div>
                                <label for="autoActivateCheck" class="block font-bold text-slate-900">Auto-Activate Accounts on Signup</label>
                                <span class="text-[10px] text-slate-500 block">New applicant profiles are marked active immediately.</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <input type="checkbox" id="showNewsAnnouncementsCheck" x-model="settings.showNewsAnnouncements" class="rounded text-amber-500">
                            <div>
                                <label for="showNewsAnnouncementsCheck" class="block font-bold text-slate-900">Enable News & Announcements Module</label>
                                <span class="text-[10px] text-slate-500 block">Show news, circulars, and bulletins in system navigations and frontend sections.</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <input type="checkbox" id="allowMultipleApplicationsCheck" x-model="settings.allowMultipleApplications" class="rounded text-amber-500">
                            <div>
                                <label for="allowMultipleApplicationsCheck" class="block font-bold text-slate-900">Allow Multiple Applications per Phone</label>
                                <span class="text-[10px] text-slate-500 block">Allow a single phone number to create/have multiple active admission applications.</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
                            <label class="block font-bold text-slate-900">Draft Expiration Timeframe (Days)</label>
                            <input type="number" x-model="settings.draftExpirationDays" min="1" max="365" class="w-full p-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <span class="text-[10px] text-slate-500 block">Number of days of inactivity before an incomplete draft application is marked as EXPIRED.</span>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                            <h5 class="font-bold text-slate-900">Password Complexity & Security Policies</h5>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Min Length</label>
                                    <input type="number" x-model="settings.passwordMinLength" class="w-full p-2 rounded-xl border border-slate-300 bg-white text-slate-900">
                                </div>
                                <div class="flex items-center space-x-2 pt-4">
                                    <input type="checkbox" id="specialCharCheck" x-model="settings.passwordRequireSpecial" class="rounded text-amber-500">
                                    <label for="specialCharCheck" class="text-[10px] font-bold text-slate-700">Require Special Character</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Top Announcement Bar Configuration -->
                <div class="border-t border-slate-100 pt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-sm">Top Announcement Bar Configuration</h4>
                            <p class="text-[10px] text-slate-500 mt-0.5">Manage the banner at the very top of the public website.</p>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-500/10 text-amber-600 rounded-lg text-[10px] font-extrabold uppercase tracking-wider">Top Banner</span>
                    </div>

                    <!-- Live Preview -->
                    <div class="bg-slate-950 rounded-2xl p-4 border border-slate-800 space-y-2">
                        <span class="text-[9px] text-slate-500 font-extrabold uppercase tracking-wider block">Live Preview (Updates instantly)</span>
                        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white text-[11px] font-semibold py-2 px-3 rounded-xl flex flex-wrap justify-between items-center select-none shadow-inner">
                            <div class="flex items-center space-x-2">
                                <span class="bg-amber-500 text-slate-950 px-2 py-0.5 rounded-full text-[9px] uppercase font-extrabold tracking-wider animate-pulse animate-duration-1000" x-text="settings.topAnnouncementBadge || '2026/2027'"></span>
                                <span class="text-white" x-text="settings.topAnnouncementText || 'Online Admissions Now Open for Undergraduate & Postgraduate Programmes'"></span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="javascript:void(0)" class="text-amber-400 transition-colors flex items-center gap-1 font-medium hover:text-amber-300">
                                    <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <span x-text="settings.topAnnouncementLinkText || 'Track Application Status'"></span>
                                </a>
                                <span class="text-blue-300">|</span>
                                <a href="javascript:void(0)" class="text-white hover:text-amber-400 transition-colors flex items-center gap-0.5">
                                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span x-text="settings.topAnnouncementPhone || '+255 22 266 8820'"></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Announcement Badge (e.g. Academic Cycle)</label>
                            <input type="text" x-model="settings.topAnnouncementBadge" placeholder="e.g. 2026/2027" class="w-full p-3 rounded-xl border border-slate-300 bg-slate-50 font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Support Phone Number</label>
                            <input type="text" x-model="settings.topAnnouncementPhone" placeholder="e.g. +255 22 266 8820" class="w-full p-3 rounded-xl border border-slate-300 bg-slate-50 font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">Announcement Text</label>
                            <input type="text" x-model="settings.topAnnouncementText" placeholder="e.g. Online Admissions Now Open for Undergraduate & Postgraduate Programmes" class="w-full p-3 rounded-xl border border-slate-300 bg-slate-50 font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Link Text</label>
                            <input type="text" x-model="settings.topAnnouncementLinkText" placeholder="e.g. Track Application Status" class="w-full p-3 rounded-xl border border-slate-300 bg-slate-50 font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Link Destination (URL / Path)</label>
                            <input type="text" x-model="settings.topAnnouncementLinkUrl" placeholder="Leave blank for default tracking route (/track-application)" class="w-full p-3 rounded-xl border border-slate-300 bg-slate-50 font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Submission Metrics -->
                <div class="border-t border-slate-100 pt-6 space-y-4">
                    <h4 class="font-extrabold text-slate-900 text-sm">Submission Access Metrics</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-indigo-50 p-5 rounded-2xl border border-indigo-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-500">Applications (With Login First)</span>
                                <span class="block text-2xl font-black text-indigo-600 mt-1" x-text="settings.applicationsWithLogin">0</span>
                            </div>
                        </div>
                        <div class="bg-amber-50 p-5 rounded-2xl border border-amber-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-500">Applications (Without Login First / Guests)</span>
                                <span class="block text-2xl font-black text-amber-600 mt-1" x-text="settings.applicationsWithoutLogin">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button @click="saveSettings()" class="gradient-btn-gold px-8 py-3.5 rounded-2xl text-slate-950 font-black text-xs shadow-xl">
                        Save System Configuration Settings &rarr;
                    </button>
                </div>
            </div>

            <!-- Troubleshooting & Diagnostics Card -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span>⚙️ System Storage & URL Repair Diagnostics</span>
                </h3>
                <p class="text-xs text-slate-500">Troubleshooting utilities to repair broken symbolic links and sanitise database absolute URLs when migrating environments.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <h4 class="font-extrabold text-slate-900 text-sm">Recreate Storage Symlink</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Check link status and recreate the symbolic link between the public web-facing directory and the actual storage directory. Useful for shared hosting or local environment conflicts.
                        </p>
                        <button @click="runStorageLinkDiag()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-extrabold text-xs shadow-sm transition-all cursor-pointer">
                            Rebuild /public/storage Symlink
                        </button>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <h4 class="font-extrabold text-slate-900 text-sm">Sanitise Database URLs</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Scans and converts hardcoded database absolute URLs (like <code>http://localhost/</code> or <code>https://www.supa.ac.tz/</code>) to clean relative paths so images load correctly regardless of domain or host.
                        </p>
                        <button @click="runDatabaseUrlRepair()" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 py-2.5 rounded-xl font-extrabold text-xs shadow-sm transition-all cursor-pointer">
                            Repair Image Path URLs
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. AUDIT LOGS TAB -->
        <div x-show="activeTab === 'logs'" x-cloak class="space-y-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-base">Security Audit Logs & Event Trail</h3>
                    <button @click="clearAuditLogs()" class="px-4 py-2 rounded-xl bg-red-600/10 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-xs">Clear Audit Logs</button>
                </div>
                
                <div class="space-y-3 text-xs">
                    <template x-for="a in auditLogs" :key="a.id">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 flex justify-between items-center">
                            <span class="font-bold text-slate-800" x-text="a.event"></span>
                            <span class="text-[10px] text-slate-500 font-bold" x-text="a.timestamp"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD HERO BANNER WITH PHOTO UPLOAD PICKER -->
        <div x-show="showBannerModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xl font-extrabold text-slate-900">Add Hero Slide Banner</h3>
                    <button @click="showBannerModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1 text-left">Banner Title *</label>
                        <input type="text" x-model="newBanner.title" placeholder="e.g. Admissions for 2026/2027 Are Now Open." class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1 text-left">Subtitle Text</label>
                        <input type="text" x-model="newBanner.subtitle" placeholder="e.g. Experience world-class open, distance, and digital higher education." class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Primary Button Text</label>
                            <input type="text" x-model="newBanner.cta" placeholder="Apply Now" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Primary Button Link</label>
                            <input type="text" x-model="newBanner.cta_link" placeholder="/register" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Secondary Button Text</label>
                            <input type="text" x-model="newBanner.secondary_cta" placeholder="Explore Programmes" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Secondary Button Link</label>
                            <input type="text" x-model="newBanner.secondary_cta_link" placeholder="/programmes" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Slide Status</label>
                            <select x-model="newBanner.status" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Upload Photo File</label>
                            <input type="file" id="newBannerFileInput" accept="image/*" @change="handleSliderPhotoUpload($event, newBanner)" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        </div>
                    </div>
                    <template x-if="newBanner.previewUrl || newBanner.image">
                        <div class="relative w-full h-32 rounded-2xl overflow-hidden border border-slate-200">
                            <img :src="newBanner.previewUrl || newBanner.image" alt="Uploaded photo preview" class="w-full h-full object-cover">
                        </div>
                    </template>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showBannerModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="addBanner()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-extrabold text-xs shadow-md">Add Banner</button>
                </div>
            </div>
        </div>

        <!-- MODAL: EDIT HERO BANNER WITH PHOTO UPLOAD PICKER -->
        <div x-show="editBannerModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xl font-extrabold text-slate-900">Edit Hero Slide Banner</h3>
                    <button @click="editBannerModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1 text-left">Banner Title *</label>
                        <input type="text" x-model="editBannerData.title" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1 text-left">Subtitle Text</label>
                        <input type="text" x-model="editBannerData.subtitle" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Primary Button Text</label>
                            <input type="text" x-model="editBannerData.cta" placeholder="Apply Now" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Primary Button Link</label>
                            <input type="text" x-model="editBannerData.cta_link" placeholder="/register" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Secondary Button Text</label>
                            <input type="text" x-model="editBannerData.secondary_cta" placeholder="Explore Programmes" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Secondary Button Link</label>
                            <input type="text" x-model="editBannerData.secondary_cta_link" placeholder="/programmes" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Slide Status</label>
                            <select x-model="editBannerData.status" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1 text-left">Change / Upload New Photo</label>
                            <input type="file" id="editBannerFileInput" accept="image/*" @change="handleSliderPhotoUpload($event, editBannerData)" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        </div>
                    </div>
                    <template x-if="editBannerData.previewUrl || editBannerData.image">
                        <div class="relative w-full h-32 rounded-2xl overflow-hidden border border-slate-200">
                            <img :src="editBannerData.previewUrl || editBannerData.image" alt="Uploaded photo preview" class="w-full h-full object-cover">
                        </div>
                    </template>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="editBannerModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="updateBanner()" class="gradient-btn px-6 py-2 rounded-2xl text-white font-extrabold text-xs shadow-md">Save Banner Changes</button>
                </div>
            </div>
        </div>

        <!-- MODAL: UPLOAD MEDIA FILE WITH PREVIEW -->
        <div x-show="showMediaModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Upload Media File / Photo</h3>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">File Title / Name</label>
                    <input type="text" x-model="newMedia.name" placeholder="e.g. Campus_Graduation_2026.jpg" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Upload Photo File</label>
                    <input type="file" id="uploadMediaFileInput" accept="image/*,application/pdf" @change="handlePhotoUpload($event, newMedia, 'preview')" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                </div>
                <template x-if="newMedia.preview">
                    <img :src="newMedia.preview" alt="Media file preview" class="w-full h-28 rounded-2xl object-cover border border-slate-300">
                </template>
                <div class="flex justify-end space-x-3 pt-2">
                    <button @click="showMediaModal = false" class="px-5 py-2 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="addMedia()" class="gradient-btn px-6 py-2 rounded-2xl text-white font-extrabold text-xs shadow-md">Upload Asset</button>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD USER ACCOUNT -->
        <div x-show="showUserModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Create Portal User Account</h3>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">User Full Name</label>
                    <input type="text" x-model="newUser.name" placeholder="e.g. Dr. Hassan Ally" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Email Address</label>
                    <input type="email" x-model="newUser.email" placeholder="hassan@supa.ac.tz" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-extrabold uppercase mb-1 text-left">Select System Roles *</label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-2xl border border-slate-200 max-h-36 overflow-y-auto">
                        <template x-for="r in rolesList" :key="r.id">
                            <label class="flex items-center gap-2 p-1.5 hover:bg-white rounded-lg cursor-pointer text-xs font-bold">
                                <input type="checkbox" :value="r.name.toUpperCase()" x-model="newUser.roles" class="rounded text-amber-500">
                                <span x-text="r.name.toUpperCase()"></span>
                            </label>
                        </template>
                    </div>
                </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Account Status</label>
                        <select x-model="newUser.status" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Active">Active</option>
                            <option value="Deactivated">Deactivated</option>
                        </select>
                    </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Initial Password (Optional)</label>
                    <input type="password" x-model="newUser.password" placeholder="Defaults to password123" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button @click="showUserModal = false" class="px-5 py-2 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="addUser()" class="gradient-btn-gold px-6 py-2 rounded-2xl text-slate-950 font-extrabold text-xs shadow-md">Create Account</button>
                </div>
            </div>
        </div>

        <!-- MODAL: EDIT USER ACCOUNT -->
        <div x-show="showEditUserModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Edit Portal User Account</h3>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">User Full Name</label>
                    <input type="text" x-model="editUserData.name" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Email Address</label>
                    <input type="email" x-model="editUserData.email" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-extrabold uppercase mb-1 text-left">Select System Roles *</label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-2xl border border-slate-200 max-h-36 overflow-y-auto">
                        <template x-for="r in rolesList" :key="r.id">
                            <label class="flex items-center gap-2 p-1.5 hover:bg-white rounded-lg cursor-pointer text-xs font-bold">
                                <input type="checkbox" :value="r.name.toUpperCase()" x-model="editUserData.roles" class="rounded text-amber-500">
                                <span x-text="r.name.toUpperCase()"></span>
                            </label>
                        </template>
                    </div>
                </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Account Status</label>
                        <select x-model="editUserData.status" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Active">Active</option>
                            <option value="Deactivated">Deactivated</option>
                        </select>
                    </div>
                <div class="border-t border-slate-100 pt-3 space-y-2">
                    <h5 class="text-[10px] font-black uppercase text-slate-500">Security & Verifications</h5>
                    <div class="flex items-center space-x-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" id="forcePasswordChangeCheck" x-model="editUserData.password_force_change" class="rounded text-amber-500">
                        <label for="forcePasswordChangeCheck">Force Password Change on next login</label>
                    </div>
                    <div class="flex items-center space-x-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" id="isLockedCheck" x-model="editUserData.is_locked" class="rounded text-amber-500">
                        <label for="isLockedCheck">Lock Account (Failed attempts block)</label>
                    </div>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 pt-1 border-t border-slate-100">
                        <span>Email Status:</span>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase"
                                  :class="editUserData.email_verified_at ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                  x-text="editUserData.email_verified_at ? 'Verified' : 'Unverified'"></span>
                            <button x-show="!editUserData.email_verified_at" @click="editUserData.email_verified_at = new Date().toISOString()" type="button" class="px-2 py-0.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-[9px]">
                                Verify Manually
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Reset Password (Optional)</label>
                    <input type="password" x-model="editUserData.password" placeholder="Leave blank to keep current password" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button @click="showEditUserModal = false" class="px-5 py-2 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="updateUser()" class="gradient-btn px-6 py-2 rounded-2xl text-white font-extrabold text-xs shadow-md">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- MODAL: DELETE USER ACCOUNT -->
        <div x-show="showDeleteUserModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mx-auto text-2xl font-bold">⚠️</div>
                <h3 class="text-lg font-extrabold text-slate-900">Delete User Account?</h3>
                <p class="text-xs text-slate-500">Are you sure you want to remove <strong class="text-slate-900" x-text="selectedUser?.name"></strong> permanently from the system?</p>
                <div class="flex justify-center space-x-3 pt-2">
                    <button @click="showDeleteUserModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="deleteUserConfirmed()" class="px-6 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md">Confirm Delete</button>
                </div>
            </div>
        </div>

        <!-- MODAL: CREATE ROLE -->
        <div x-show="showAddRoleModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Create System Role</h3>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Role Key (e.g. hr_officer, content_editor) *</label>
                    <input type="text" x-model="newRole.name" placeholder="e.g. content_editor" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Display Name *</label>
                    <input type="text" x-model="newRole.display_name" placeholder="e.g. Content Editor" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Description</label>
                    <textarea x-model="newRole.description" rows="3" placeholder="Explain the responsibilities of this role..." class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button @click="showAddRoleModal = false" class="px-5 py-2 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="addRole()" class="gradient-btn-gold px-6 py-2 rounded-2xl text-slate-950 font-extrabold text-xs shadow-md">Create Role</button>
                </div>
            </div>
        </div>

        <!-- MODAL: DELETE ROLE CONFIRMATION -->
        <div x-show="showDeleteRoleModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mx-auto text-2xl font-bold">⚠️</div>
                <h3 class="text-lg font-extrabold text-slate-900">Delete System Role?</h3>
                <p class="text-xs text-slate-500">Are you sure you want to delete role <strong class="text-slate-900" x-text="selectedRoleForDelete?.display_name"></strong>? This will detach it from all users and remove its permission records.</p>
                <div class="flex justify-center space-x-3 pt-2">
                    <button @click="showDeleteRoleModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="deleteRoleConfirmed()" class="px-6 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md">Confirm Delete</button>
                </div>
            </div>
        </div>

        <!-- MODAL: DISPATCH COMM -->
        <div x-show="showCommModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Dispatch Notification Alert</h3>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Alert Title</label>
                    <input type="text" x-model="newComm.title" placeholder="Admission Deadline Notice" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold whitespace-nowrap overflow-hidden text-ellipsis">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Recipient (Phone or Email)</label>
                    <input type="text" x-model="newComm.recipient" placeholder="+255755100101 or email" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase mb-1">Channel</label>
                    <select x-model="newComm.channel" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="SMS">SMS</option>
                        <option value="Email">Email</option>
                        <option value="WhatsApp">WhatsApp</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button @click="showCommModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="sendComm()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-extrabold text-xs shadow-md">Dispatch Alert</button>
                </div>
            </div>
        </div>
        <!-- MODAL: PUBLISH NEWS ARTICLE -->
        <div x-show="showNewsModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-2xl w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xl font-extrabold text-slate-900">Publish New Article & Circular</h3>
                    <button @click="showNewsModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Article Title *</label>
                        <input type="text" x-model="newNews.title" placeholder="e.g. STTC & OUT Release 2026/2027 Admission Selection List" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Short Summary (Excerpt)</label>
                        <textarea x-model="newNews.summary" rows="2" placeholder="Brief 1-2 sentence overview of the circular or announcement..." class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Full Article Content *</label>
                        <textarea x-model="newNews.content" rows="6" placeholder="Full announcement text, admission guidelines, instructions..." class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Publication Date</label>
                            <input type="date" x-model="newNews.published_at" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Feature on Homepage Slider</label>
                            <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold cursor-pointer">
                                <input type="checkbox" x-model="newNews.is_featured" class="w-4 h-4 rounded text-amber-500">
                                <span>Mark as Featured Article</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Upload Cover Photo Image</label>
                        <input type="file" id="newNewsFileInput" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showNewsModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="createNews()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-extrabold text-xs shadow-md">Publish News</button>
                </div>
            </div>
        </div>

        <!-- MODAL: EDIT NEWS ARTICLE -->
        <div x-show="showEditNewsModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-2xl w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xl font-extrabold text-slate-900">Edit News Article</h3>
                    <button @click="showEditNewsModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Article Title *</label>
                        <input type="text" x-model="editNewsData.title" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Short Summary (Excerpt)</label>
                        <textarea x-model="editNewsData.summary" rows="2" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Full Article Content *</label>
                        <textarea x-model="editNewsData.content" rows="6" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Publication Date</label>
                            <input type="date" x-model="editNewsData.published_at" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Feature on Homepage</label>
                            <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold cursor-pointer">
                                <input type="checkbox" x-model="editNewsData.is_featured" class="w-4 h-4 rounded text-amber-500">
                                <span>Mark as Featured Article</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Change Cover Photo Image (Optional)</label>
                        <template x-if="editNewsData.image">
                            <div class="mb-3 relative w-32 h-20 rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                                <img :src="editNewsData.image" alt="Current Cover" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <input type="file" id="editNewsFileInput" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showEditNewsModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="updateNews()" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- MODAL: DELETE NEWS ARTICLE -->
        <div x-show="showDeleteNewsModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mx-auto text-2xl font-bold">⚠️</div>
                <h3 class="text-lg font-extrabold text-slate-900">Delete News Article?</h3>
                <p class="text-xs text-slate-500">Are you sure you want to remove article <strong class="text-slate-900" x-text="selectedNews?.title"></strong> permanently?</p>
                <div class="flex justify-center space-x-3 pt-2">
                    <button @click="showDeleteNewsModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="deleteNewsConfirmed()" class="px-6 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md">Confirm Delete</button>
                </div>
            </div>
        </div>

        <!-- MODAL: VIEW CONTACT MESSAGE INQUIRY -->
        <div x-show="showContactModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-xl w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedContact?.subject"></h3>
                        <p class="text-[11px] text-slate-500" x-text="'Sent on ' + selectedContact?.date"></p>
                    </div>
                    <button @click="showContactModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1">
                        <div class="font-extrabold text-slate-900" x-text="'Sender: ' + selectedContact?.name"></div>
                        <div>Email: <a :href="'mailto:' + selectedContact?.email" class="text-blue-500 font-bold hover:underline" x-text="selectedContact?.email"></a></div>
                        <div x-show="selectedContact?.phone">Phone: <a :href="'tel:' + selectedContact?.phone" class="text-blue-500 font-bold hover:underline" x-text="selectedContact?.phone"></a></div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 font-medium leading-relaxed text-slate-800 whitespace-pre-wrap" x-text="selectedContact?.message"></div>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                    <button @click="deleteContact(selectedContact)" class="px-4 py-2 rounded-2xl bg-red-600/10 text-red-500 font-extrabold text-xs hover:bg-red-600 hover:text-white">Delete Message</button>
                    <div class="flex gap-2">
                        <a :href="'mailto:' + selectedContact?.email + '?subject=Re: ' + selectedContact?.subject" class="px-5 py-2 rounded-2xl bg-amber-500 text-slate-950 font-extrabold text-xs shadow-md">Reply via Email</a>
                        <button @click="showContactModal = false" class="px-5 py-2 rounded-2xl bg-slate-200 text-xs font-extrabold">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD PROGRAMME CATEGORY -->
        <div x-show="showAddCategoryModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xl font-extrabold text-slate-900">Add Mega Menu Category</h3>
                    <button @click="showAddCategoryModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Category Code / Badge (e.g. UG, PG, DIP, FC) *</label>
                        <input type="text" x-model="newCategory.code" placeholder="DIP" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Category Title *</label>
                        <input type="text" x-model="newCategory.title" placeholder="Diploma Courses" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Subtitle / Description Excerpt</label>
                        <input type="text" x-model="newCategory.subtitle" placeholder="2-Year Ordinary Diploma Qualifications" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Badge Accent Color</label>
                        <select x-model="newCategory.color" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                            <option value="blue">Blue (Undergraduate style)</option>
                            <option value="amber">Amber/Gold (Postgraduate style)</option>
                            <option value="emerald">Emerald/Green (Foundation style)</option>
                            <option value="purple">Purple Accent</option>
                            <option value="rose">Rose/Red Accent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Category Status</label>
                        <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold cursor-pointer">
                            <input type="checkbox" x-model="newCategory.is_active" class="w-4 h-4 rounded text-amber-500">
                            <span>Enabled (Visible on Public Navigation Mega-Menu)</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showAddCategoryModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="addCategory()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-extrabold text-xs shadow-md">Add Category</button>
                </div>
            </div>
        </div>

        <!-- MODAL: EDIT PROGRAMME CATEGORY -->
        <div x-show="showEditCategoryModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xl font-extrabold text-slate-900">Edit Mega Menu Category</h3>
                    <button @click="showEditCategoryModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Category Code / Badge *</label>
                        <input type="text" x-model="editCategoryData.code" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Category Title *</label>
                        <input type="text" x-model="editCategoryData.title" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Subtitle / Description Excerpt</label>
                        <input type="text" x-model="editCategoryData.subtitle" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Badge Accent Color</label>
                        <select x-model="editCategoryData.color" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                            <option value="blue">Blue (Undergraduate style)</option>
                            <option value="amber">Amber/Gold (Postgraduate style)</option>
                            <option value="emerald">Emerald/Green (Foundation style)</option>
                            <option value="purple">Purple Accent</option>
                            <option value="rose">Rose/Red Accent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Category Status</label>
                        <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold cursor-pointer">
                            <input type="checkbox" x-model="editCategoryData.is_active" class="w-4 h-4 rounded text-amber-500">
                            <span>Enabled (Visible on Public Navigation Mega-Menu)</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showEditCategoryModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="updateCategory()" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD ACADEMIC PROGRAMME -->
        <div x-show="showAddProgrammeModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xl font-extrabold text-slate-900">Add New Academic Programme</h3>
                    <button @click="showAddProgrammeModal = false" class="text-slate-500 hover:text-slate-600 font-bold">✕</button>
                </div>
                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Code *</label>
                            <input type="text" x-model="newProg.code" placeholder="e.g. BIT" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none uppercase">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-extrabold uppercase mb-1">Programme Title *</label>
                            <input type="text" x-model="newProg.name" placeholder="e.g. Bachelor of Information Technology" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Duration (Years)</label>
                            <input type="number" x-model="newProg.duration_years" min="1" max="10" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Annual Tuition Fee (TZS)</label>
                            <input type="number" x-model="newProg.annual_fee" min="0" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Department</label>
                            <input type="text" x-model="newProg.department" placeholder="Department of ICT" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase mb-1">Faculty</label>
                            <input type="text" x-model="newProg.faculty" placeholder="Faculty of Science & Tech" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold uppercase mb-1">Upload Programme Cover Photo Image</label>
                        <input type="file" id="newProgFileInput" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold cursor-pointer">
                            <input type="checkbox" x-model="newProg.is_active" class="w-4 h-4 rounded text-amber-500">
                            <span>Featured on Homepage Showcase</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showAddProgrammeModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="addProgramme()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-extrabold text-xs shadow-md">Create & Publish</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cmsDesk', () => ({
                activeTab: (window.location.hash && (window.location.hash === '#reports' || {{ auth()->user()->hasPermissionTo("manage_settings") ? 'true' : 'false' }})) ? window.location.hash.replace('#', '') : '{{ auth()->user()->hasPermissionTo("manage_settings") ? "cms" : "reports" }}',
                cmsSubTab: 'sliders',

                // Reports Filters State
                reportFilter: {
                    year: '',
                    month: '',
                    start_date: '',
                    end_date: '',
                    status: ''
                },

                init() {
                    this.updateTabFromHash();
                },

                resetReportFilters() {
                    this.reportFilter = {
                        year: '',
                        month: '',
                        start_date: '',
                        end_date: '',
                        status: ''
                    };
                },

                getReportUrl(type, format) {
                    let baseUrl = format === 'pdf' ? "{{ route('admin.reports.pdf') }}" : "{{ url('/api/v1/admin/export-report') }}";
                    let params = new URLSearchParams();
                    params.append('type', type);
                    if (format === 'pdf') {
                        params.append('download', '1');
                    }
                    if (this.reportFilter.year) params.append('year', this.reportFilter.year);
                    if (this.reportFilter.month) params.append('month', this.reportFilter.month);
                    if (this.reportFilter.start_date) params.append('start_date', this.reportFilter.start_date);
                    if (this.reportFilter.end_date) params.append('end_date', this.reportFilter.end_date);
                    if (this.reportFilter.status) params.append('status', this.reportFilter.status);
                    return `${baseUrl}?${params.toString()}`;
                },

                updateTabFromHash() {
                    if (window.location.hash) {
                        const h = window.location.hash.replace('#', '');
                        const allowed = {{ auth()->user()->hasPermissionTo("manage_settings") ? 'true' : 'false' }};
                        if (h === 'reports') {
                            this.activeTab = h;
                        } else if (allowed && ['cms', 'media', 'users', 'comm', 'settings', 'logs'].includes(h)) {
                            this.activeTab = h;
                        }
                    }
                },

                // 1. Sliders Manager State (CRUD)
                banners: @json($banners),
                pageBanners: @json($pageBanners),
                policies: @json($policies),
                showBannerModal: false,
                editBannerModal: false,
                newBanner: { title: '', subtitle: '', cta: 'Apply Now', cta_link: '/register', secondary_cta: 'Explore Programmes', secondary_cta_link: '/programmes', image: 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070', status: 'Active', previewUrl: null },
                editBannerData: { id: null, title: '', subtitle: '', cta: '', cta_link: '', secondary_cta: '', secondary_cta_link: '', image: '', status: 'Active', previewUrl: null },

                // 2. About Section Manager State
                aboutContent: @json($defaultAbout),

                // 3. Featured Programmes Showcase Manager State
                featuredProgrammes: @json($programmesList),
                showAddProgrammeModal: false,
                newProg: { code: '', name: '', duration_years: 3, annual_fee: 1200000, department: '', faculty: '', description: '', is_active: true },
                programmeCategories: @json($programmeCategories),
                catalogHeader: @json($catalogHeader),
                showAddCategoryModal: false,
                showEditCategoryModal: false,
                showInlineAddCategory: false,
                editingCategoryIdx: null,
                newCategory: { code: 'DIP', title: '', subtitle: '', color: 'blue', is_active: true },
                editCategoryData: { idx: null, code: '', title: '', subtitle: '', color: 'blue', is_active: true },
                inlineCategoryData: { idx: null, code: '', title: '', subtitle: '', color: 'blue', is_active: true },

                // 4. Footer & Social Links State
                footerContent: @json($defaultFooter),

                // 5. Logo & University Branding Identity State
                brandIdentity: {
                    universityName: @json($logos['university_name'] ?? "SINGIDA TEACHERS' TRAINING COLLEGE (STTC) & OUT"),
                    sttcLogo: @json($logos['sttc_logo'] ?? ''),
                    outLogo: @json($logos['out_logo'] ?? ''),
                    officialSeal: @json($logos['official_seal'] ?? ''),
                    registrarSignature: @json($logos['registrar_signature'] ?? ''),
                    systemLogo: @json($logos['system_logo'] ?? ''),
                    loginBackgroundImage: @json($logos['login_background_image'] ?? ''),
                    footerCopyright: @json($logos['footer_copyright'] ?? '© ' . date('Y') . ' SUPA / OUT University Admission Management System. All rights reserved.'),
                    developerName: @json($logos['developer_name'] ?? 'Reliance Solutions & Technology'),
                    developerUrl: @json($logos['developer_url'] ?? 'http://www.reliancesolutions.co.tz')
                },

                // Media Assets Local State (CRUD)
                mediaAssets: @json($mediaFiles),
                showMediaModal: false,
                newMedia: { name: '', category: 'Prospectus', preview: '' },

                // Users Local State (CRUD)
                userSearch: '',
                userRoleFilter: 'ALL',
                userStatusFilter: 'ALL',
                inlineEditingUserId: null,
                inlineEditUserData: {},
                usersList: @json($usersList),
                showUserModal: false,
                showEditUserModal: false,
                showDeleteUserModal: false,
                selectedUser: null,
                newUser: { name: '', email: '', roles: ['ADMISSION_OFFICER'], password: '', status: 'Active' },
                editUserData: { id: null, name: '', email: '', roles: [], status: 'Active', password: '' },

                userSubTab: 'users',
                rolesList: @json($roles),
                permissionsList: @json($permissions),
                showAddRoleModal: false,
                showDeleteRoleModal: false,
                selectedRoleForDelete: null,
                newRole: { name: '', display_name: '', description: '' },
                selectedRoleId: null,
                selectedRole: null,
                selectedRolePermissionIds: [],

                get groupedPermissions() {
                    let grouped = {};
                    this.permissionsList.forEach(p => {
                        let g = p.group ? p.group.toUpperCase() : 'GENERAL';
                        if (!grouped[g]) grouped[g] = [];
                        grouped[g].push(p);
                    });
                    return grouped;
                },

                selectRole(role) {
                    this.selectedRoleId = role.id;
                    this.selectedRole = role;
                    this.selectedRolePermissionIds = role.permissions ? role.permissions.map(p => p.id) : [];
                },

                addRole() {
                    if (!this.newRole.name || !this.newRole.display_name) {
                        toast('Role name and display name are required.', 'error');
                        return;
                    }
                    axios.post('{{ route('admin.cms.roles.store') }}', this.newRole)
                        .then(res => {
                            if (res.data.success && res.data.role) {
                                this.rolesList.push(res.data.role);
                                toast(res.data.message || 'Role created successfully!', 'success');
                                this.showAddRoleModal = false;
                                this.newRole = { name: '', display_name: '', description: '' };
                            }
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error creating role', 'error');
                        });
                },

                confirmDeleteRole(role) {
                    this.selectedRoleForDelete = role;
                    this.showDeleteRoleModal = true;
                },

                deleteRoleConfirmed() {
                    if (!this.selectedRoleForDelete) return;
                    axios.delete('{{ url('/admin/cms/roles') }}/' + this.selectedRoleForDelete.id)
                        .then(res => {
                            if (res.data.success) {
                                this.rolesList = this.rolesList.filter(r => r.id !== this.selectedRoleForDelete.id);
                                if (this.selectedRoleId === this.selectedRoleForDelete.id) {
                                    this.selectedRoleId = null;
                                    this.selectedRole = null;
                                    this.selectedRolePermissionIds = [];
                                }
                                toast(res.data.message || 'Role deleted successfully.', 'success');
                            }
                        })
                        .catch(err => {
                            toast('Error deleting role.', 'error');
                        });
                    this.showDeleteRoleModal = false;
                    this.selectedRoleForDelete = null;
                },

                saveRolePermissions() {
                    if (!this.selectedRole) return;
                    axios.post('{{ url('/admin/cms/roles') }}/' + this.selectedRole.id + '/permissions', {
                        permissions: this.selectedRolePermissionIds
                    })
                    .then(res => {
                        if (res.data.success && res.data.role) {
                            const idx = this.rolesList.findIndex(r => r.id === this.selectedRole.id);
                            if (idx !== -1) {
                                this.rolesList[idx] = res.data.role;
                            }
                            this.selectedRole = res.data.role;
                            toast(res.data.message || 'Permissions updated successfully!', 'success');
                        }
                    })
                    .catch(err => {
                        toast('Error saving permissions.', 'error');
                    });
                },

                // Communication Logs (CRUD)
                commLogs: [
                    { id: 1, title: 'OTP Security Dispatch', channel: 'SMS & Email', recipient: 'applicant1@supa.ac.tz', status: 'DELIVERED', timestamp: 'Just Now' },
                    { id: 2, title: 'Payment Control Number Alert', channel: 'SMS', recipient: '+255755100101', status: 'DELIVERED', timestamp: '10 mins ago' }
                ],
                showCommModal: false,
                newComm: { title: '', channel: 'SMS', recipient: '' },

                // System Settings State
                settings: @json($systemSettings),

                // Audit Logs State
                auditLogs: @json($auditLogs),

                // News Local State (CRUD)
                newsList: @json($newsList),
                showNewsModal: false,
                showEditNewsModal: false,
                showDeleteNewsModal: false,
                selectedNews: null,
                newNews: { title: '', summary: '', content: '', published_at: '{{ date('Y-m-d') }}', is_featured: false },
                editNewsData: { id: null, title: '', summary: '', content: '', published_at: '', is_featured: false, image: null },

                // Contact Local State & Messages (CRUD)
                contactMessages: @json($contactMessages),
                contactSettings: @json($contactSettings),
                contactFilter: 'ALL',
                selectedContact: null,
                showContactModal: false,

                // Helper for handling photo upload file input & generating instant preview URL
                handlePhotoUpload(event, targetObj, propName = 'image') {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            targetObj[propName] = e.target.result;
                            toast('Photo uploaded & preview generated!', 'success');
                        };
                        reader.readAsDataURL(file);
                    }
                },

                // Methods
                savePageBanner(key, fileInputId) {
                    const fileInput = document.getElementById(fileInputId);
                    if (!fileInput || !fileInput.files[0]) {
                        toast('Please select an image file first.', 'error');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('key', key);
                    formData.append('image', fileInput.files[0]);

                    axios.post('{{ route('admin.cms.page-banners') }}', formData)
                        .then(res => {
                            if (res.data.success) {
                                this.pageBanners[key] = res.data.url;
                                toast(res.data.message || 'Page banner background updated successfully!', 'success');
                            }
                            fileInput.value = '';
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error updating page banner background', 'error');
                        });
                },

                removePageBanner(key) {
                    if (!confirm('Are you sure you want to revert this banner background to default?')) return;

                    axios.post('{{ route('admin.cms.page-banners.delete') }}', { key: key })
                        .then(res => {
                            if (res.data.success) {
                                this.pageBanners[key] = null;
                                toast(res.data.message || 'Page banner background image removed.', 'success');
                            }
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error removing banner background', 'error');
                        });
                },

                savePolicyContent() {
                    const formData = new FormData();
                    formData.append('privacy_policy_content', this.policies.privacy || '');
                    formData.append('terms_conditions_content', this.policies.terms || '');

                    axios.post('{{ route('admin.cms.policies') }}', formData)
                        .then(res => {
                            toast(res.data.message || 'Policies updated successfully!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error updating policies', 'error');
                        });
                },

                handleSliderPhotoUpload(event, targetObj, previewPropName = 'previewUrl') {
                    const file = event.target.files[0];
                    if (file) {
                        // Generate object URL for instant zero-payload client-side preview
                        if (targetObj[previewPropName] && targetObj[previewPropName].startsWith('blob:')) {
                            URL.revokeObjectURL(targetObj[previewPropName]);
                        }
                        targetObj[previewPropName] = URL.createObjectURL(file);
                        toast('Image selected & preview loaded.', 'success');
                    }
                },

                saveSlidersToServer(targetId = null, fileInputId = null) {
                    const formData = new FormData();
                    
                    // Clean banners array to make sure it contains no blob or base64 preview URLs
                    const cleanBanners = this.banners.map(b => {
                        const copy = { ...b };
                        delete copy.previewUrl; // Ensure we don't send previewUrl key
                        return copy;
                    });
                    
                    formData.append('banners', JSON.stringify(cleanBanners));
                    if (targetId) formData.append('target_id', targetId);
                    
                    if (fileInputId) {
                        const fileInput = document.getElementById(fileInputId);
                        if (fileInput && fileInput.files[0]) {
                            formData.append('image', fileInput.files[0]);
                        }
                    }

                    axios.post('{{ route('admin.cms.sliders') }}', formData)
                        .then(res => {
                            if (res.data.banners) {
                                this.banners = res.data.banners.map(b => ({
                                    ...b,
                                    previewUrl: null
                                }));
                            }
                            toast('Hero Sliders saved & published successfully!', 'success');
                            
                            // Reset file inputs
                            if (fileInputId) {
                                const fileInput = document.getElementById(fileInputId);
                                if (fileInput) fileInput.value = '';
                            }
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error saving sliders', 'error');
                        });
                },

                openAddBanner() {
                    this.newBanner = { title: '', subtitle: '', cta: 'Apply Now', cta_link: '/register', secondary_cta: 'Explore Programmes', secondary_cta_link: '/programmes', image: 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070', status: 'Active', previewUrl: null };
                    this.showBannerModal = true;
                    setTimeout(() => {
                        const fileInput = document.getElementById('newBannerFileInput');
                        if (fileInput) fileInput.value = '';
                    }, 0);
                },

                addBanner() {
                    if (!this.newBanner.title) { toast('Title required', 'error'); return; }
                    const newId = Date.now();
                    
                    // Create the slide object with a placeholder image
                    const slide = {
                        id: newId,
                        title: this.newBanner.title,
                        subtitle: this.newBanner.subtitle || '',
                        cta: this.newBanner.cta || '',
                        cta_link: this.newBanner.cta_link || '',
                        secondary_cta: this.newBanner.secondary_cta || '',
                        secondary_cta_link: this.newBanner.secondary_cta_link || '',
                        image: 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070', // Default/placeholder
                        status: this.newBanner.status || 'Active'
                    };

                    this.banners.unshift(slide);
                    this.showBannerModal = false;
                    
                    this.saveSlidersToServer(newId, 'newBannerFileInput');
                },

                openEditBanner(b) {
                    this.editBannerData = {
                        id: b.id,
                        title: b.title || '',
                        subtitle: b.subtitle || '',
                        cta: b.cta || '',
                        cta_link: b.cta_link || '',
                        secondary_cta: b.secondary_cta || '',
                        secondary_cta_link: b.secondary_cta_link || '',
                        image: b.image || '',
                        status: b.status || 'Active',
                        previewUrl: null
                    };
                    this.editBannerModal = true;
                    // Clear the file input
                    setTimeout(() => {
                        const fileInput = document.getElementById('editBannerFileInput');
                        if (fileInput) fileInput.value = '';
                    }, 0);
                },

                updateBanner() {
                    const idx = this.banners.findIndex(b => b.id === this.editBannerData.id);
                    if (idx !== -1) {
                        const originalImage = this.banners[idx].image;
                        
                        // Update fields locally
                        this.banners[idx] = {
                            id: this.editBannerData.id,
                            title: this.editBannerData.title,
                            subtitle: this.editBannerData.subtitle || '',
                            cta: this.editBannerData.cta || '',
                            cta_link: this.editBannerData.cta_link || '',
                            secondary_cta: this.editBannerData.secondary_cta || '',
                            secondary_cta_link: this.editBannerData.secondary_cta_link || '',
                            image: originalImage, // Revert/keep the original URL so no blobs/base64 strings are saved
                            status: this.editBannerData.status || 'Active'
                        };

                        this.saveSlidersToServer(this.editBannerData.id, 'editBannerFileInput');
                    }
                    this.editBannerModal = false;
                },

                deleteBanner(id) {
                    if (!confirm('Are you sure you want to delete this hero slide?')) return;
                    this.banners = this.banners.filter(b => b.id !== id);
                    this.saveSlidersToServer();
                },

                saveAboutContent() {
                    const formData = new FormData();
                    formData.append('title', this.aboutContent.title || '');
                    formData.append('badge', this.aboutContent.badge || '');
                    formData.append('description', this.aboutContent.description || '');
                    formData.append('mission', this.aboutContent.mission || '');
                    formData.append('vision', this.aboutContent.vision || '');
                    formData.append('verificationText', this.aboutContent.verificationText || '');

                    const campusInput = document.getElementById('aboutCampusFileInput');
                    if (campusInput && campusInput.files[0]) {
                        formData.append('campus_image', campusInput.files[0]);
                    }

                    axios.post('{{ route('admin.cms.about') }}', formData)
                        .then(res => {
                            toast(res.data.message || 'About University Section updated successfully!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error updating About section', 'error');
                        });
                },

                saveCtaContent() {
                    const formData = new FormData();
                    formData.append('ctaBadge', this.aboutContent.ctaBadge || '');
                    formData.append('ctaTitle', this.aboutContent.ctaTitle || '');
                    formData.append('ctaDescription', this.aboutContent.ctaDescription || '');

                    const ctaInput = document.getElementById('ctaBackgroundFileInput');
                    if (ctaInput && ctaInput.files[0]) {
                        formData.append('cta_background_image', ctaInput.files[0]);
                    }

                    axios.post('{{ route('admin.cms.about') }}', formData)
                        .then(res => {
                            toast(res.data.message || 'Academic Journey Banner updated successfully!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error updating Journey section', 'error');
                        });
                },

                toggleFeatured(p) {
                    p.featured = !p.featured;
                    axios.post('{{ route('admin.cms.programmes.featured') }}', { programme_id: p.id })
                        .then(res => {
                            toast(res.data.message || p.code + ' featured state toggled!', 'success');
                        })
                        .catch(err => {
                            toast('Error updating programme state', 'error');
                        });
                },

                uploadProgrammePhoto(event, p) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        p.image = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    const formData = new FormData();
                    formData.append('programme_id', p.id);
                    formData.append('image', file);

                    axios.post('{{ route('admin.cms.programmes.photo') }}', formData)
                        .then(res => {
                            if (res.data.image) {
                                p.image = res.data.image;
                            }
                            toast(res.data.message || p.code + ' cover photo uploaded & saved!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error uploading programme photo', 'error');
                        });
                },

                openAddProgrammeModal() {
                    this.newProg = { code: '', name: '', duration_years: 3, annual_fee: 1200000, department: '', faculty: '', description: '', is_active: true };
                    this.showAddProgrammeModal = true;
                },

                addProgramme() {
                    if (!this.newProg.code || !this.newProg.name) {
                        toast('Programme Code and Name required', 'error');
                        return;
                    }
                    const formData = new FormData();
                    formData.append('code', this.newProg.code);
                    formData.append('name', this.newProg.name);
                    formData.append('duration_years', this.newProg.duration_years);
                    formData.append('annual_fee', this.newProg.annual_fee);
                    formData.append('department', this.newProg.department || '');
                    formData.append('faculty', this.newProg.faculty || '');
                    formData.append('description', this.newProg.description || '');
                    formData.append('is_active', this.newProg.is_active ? '1' : '0');
                    const fileInput = document.getElementById('newProgFileInput');
                    if (fileInput && fileInput.files[0]) {
                        formData.append('photo_file', fileInput.files[0]);
                    }

                    axios.post('{{ route('admin.programmes.store') }}', formData)
                        .then(res => {
                            if (res.data.programme) {
                                const prog = res.data.programme;
                                this.featuredProgrammes.unshift({
                                    id: prog.id,
                                    code: prog.code,
                                    name: prog.name,
                                    department: prog.department || '',
                                    faculty: prog.faculty || '',
                                    duration_years: prog.duration_years || 3,
                                    annual_fee: prog.annual_fee || 0,
                                    featured: !!prog.is_active,
                                    image: prog.image || 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800'
                                });
                            }
                            this.showAddProgrammeModal = false;
                            toast('New programme added to catalog and featured showcase!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error creating programme', 'error');
                        });
                },

                saveFooterContent() {
                    axios.post('{{ route('admin.cms.footer') }}', this.footerContent)
                        .then(res => {
                            toast(res.data.message || 'Footer & Contact details updated successfully!', 'success');
                        })
                        .catch(err => {
                            toast('Error updating footer content', 'error');
                        });
                },

                saveBrandIdentity() {
                    const form = document.getElementById('logoManagementForm');
                    if (!form) return;
                    const formData = new FormData(form);
                    axios.post('{{ route('admin.cms.logos') }}', formData)
                    .then(res => {
                        toast('Institutional logos & branding updated successfully!', 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    })
                    .catch(err => {
                        toast(err.response?.data?.message || 'Error updating logos', 'error');
                    });
                },

                addMedia() {
                    const fileInput = document.getElementById('uploadMediaFileInput');
                    if (!fileInput || !fileInput.files[0]) {
                        toast('Please select a file to upload', 'error');
                        return;
                    }
                    const formData = new FormData();
                    formData.append('media_file', fileInput.files[0]);

                    axios.post('{{ route('admin.cms.media.store') }}', formData)
                        .then(res => {
                            if (res.data.file) {
                                this.mediaAssets.unshift(res.data.file);
                            }
                            this.showMediaModal = false;
                            toast(res.data.message || 'Media file uploaded successfully!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error uploading file', 'error');
                        });
                },

                deleteMedia(f) {
                    if (!confirm('Delete this media file permanently?')) return;
                    axios.post('{{ route('admin.cms.media.delete') }}', { filename: f.name })
                        .then(res => {
                            this.mediaAssets = this.mediaAssets.filter(m => m.name !== f.name);
                            toast(res.data.message || 'Media asset deleted.', 'success');
                        })
                        .catch(err => {
                            toast('Error deleting media file', 'error');
                        });
                },

                filteredUsers() {
                    return this.usersList.filter(u => {
                        const q = this.userSearch.trim().toLowerCase();
                        const matchQuery = !q || 
                            (u.name && u.name.toLowerCase().includes(q)) ||
                            (u.email && u.email.toLowerCase().includes(q)) ||
                            (u.role && u.role.toLowerCase().includes(q));
                        const matchRole = this.userRoleFilter === 'ALL' || u.role === this.userRoleFilter;
                        const matchStatus = this.userStatusFilter === 'ALL' || u.status === this.userStatusFilter;
                        return matchQuery && matchRole && matchStatus;
                    });
                },

                startInlineUserEdit(u) {
                    this.inlineEditingUserId = u.id;
                    this.inlineEditUserData = JSON.parse(JSON.stringify(u));
                },

                cancelInlineUserEdit() {
                    this.inlineEditingUserId = null;
                    this.inlineEditUserData = {};
                },

                async saveInlineUserEdit() {
                    await this.submitUserUpdate(this.inlineEditUserData);
                    this.inlineEditingUserId = null;
                },

                openEditUser(u) {
                    this.editUserData = { ...JSON.parse(JSON.stringify(u)), password: '' };
                    if (!this.editUserData.roles) {
                        this.editUserData.roles = [u.role ? u.role.toUpperCase() : 'APPLICANT'];
                    }
                    this.showEditUserModal = true;
                },

                async updateUser() {
                    await this.submitUserUpdate(this.editUserData);
                    this.showEditUserModal = false;
                },

                async submitUserUpdate(userData) {
                    try {
                        const res = await axios.put('{{ url('/admin/cms/users') }}/' + userData.id, userData);
                        if (res.data.success && res.data.user) {
                            const idx = this.usersList.findIndex(u => u.id === userData.id);
                            if (idx !== -1) {
                                this.usersList[idx] = { ...res.data.user };
                            }
                            toast(res.data.message || 'User account updated successfully!', 'success');
                        } else {
                            const idx = this.usersList.findIndex(u => u.id === userData.id);
                            if (idx !== -1) {
                                this.usersList[idx] = { ...userData };
                            }
                            toast('User account updated!', 'success');
                        }
                    } catch (err) {
                        const idx = this.usersList.findIndex(u => u.id === userData.id);
                        if (idx !== -1) {
                            this.usersList[idx] = { ...userData };
                        }
                        toast('User account updated!', 'success');
                    }
                },

                async toggleUserStatus(u) {
                    try {
                        const res = await axios.post('{{ url('/admin/cms/users') }}/' + u.id + '/status');
                        if (res.data.success) {
                            u.status = res.data.status;
                            toast(res.data.message, 'success');
                        }
                    } catch (err) {
                        u.status = u.status === 'Active' ? 'Deactivated' : 'Active';
                        toast('User status updated', 'success');
                    }
                },

                addUser() {
                    if (!this.newUser.name || !this.newUser.email) { toast('Name & Email required', 'error'); return; }
                    axios.post('{{ route('admin.cms.users.store') }}', this.newUser)
                        .then(res => {
                            if (res.data.user) {
                                this.usersList.unshift(res.data.user);
                            } else {
                                this.usersList.unshift({ id: Date.now(), ...this.newUser });
                            }
                            this.showUserModal = false;
                            this.newUser = { name: '', email: '', roles: ['ADMISSION_OFFICER'], password: '', status: 'Active' };
                            toast(res.data.message || 'New Portal User Account created!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error creating user', 'error');
                        });
                },

                confirmDeleteUser(u) {
                    this.selectedUser = u;
                    this.showDeleteUserModal = true;
                },

                async deleteUserConfirmed() {
                    if (this.selectedUser) {
                        try {
                            await axios.delete('{{ url('/admin/cms/users') }}/' + this.selectedUser.id);
                        } catch (err) {}
                        this.usersList = this.usersList.filter(u => u.id !== this.selectedUser.id);
                        toast('User account removed permanently.', 'success');
                    }
                    this.showDeleteUserModal = false;
                    this.selectedUser = null;
                },

                sendComm() {
                    if (!this.newComm.recipient) { toast('Recipient required', 'error'); return; }
                    axios.post('{{ route('admin.cms.comm.send') }}', this.newComm)
                        .then(res => {
                            this.commLogs.unshift({
                                id: Date.now(),
                                title: this.newComm.title || 'Direct Notification Alert',
                                channel: this.newComm.channel || 'SMS',
                                recipient: this.newComm.recipient,
                                status: 'DELIVERED',
                                timestamp: 'Just Now'
                            });
                            this.showCommModal = false;
                            this.newComm = { title: '', channel: 'SMS', recipient: '' };
                            toast(res.data.message || 'Communication alert dispatched!', 'success');
                        })
                        .catch(err => {
                            toast('Error dispatching alert', 'error');
                        });
                },

                saveSettings() {
                    axios.post('{{ route('admin.cms.settings') }}', this.settings)
                        .then(res => {
                            toast(res.data.message || 'System Settings saved!', 'success');
                        })
                        .catch(err => {
                            toast('Error saving system settings', 'error');
                        });
                },

                runStorageLinkDiag() {
                    if (!confirm('Recreate the storage symbolic link? This will clear any existing broken links or folders named public/storage.')) return;
                    axios.post('{{ route('admin.cms.storage-link') }}')
                        .then(res => {
                            toast(res.data.message || 'Symbolic link successfully created!', 'success');
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error creating symbolic link', 'error');
                        });
                },

                runDatabaseUrlRepair() {
                    if (!confirm('Scan and fix all absolute storage URLs in the database? This converts absolute paths to relative paths.')) return;
                    axios.post('{{ route('admin.cms.fix-urls') }}')
                        .then(res => {
                            toast(res.data.message || 'Database URLs fixed!', 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        })
                        .catch(err => {
                            toast(err.response?.data?.message || 'Error repairing URLs', 'error');
                        });
                },

                clearAuditLogs() {
                    if (!confirm('Clear all audit security logs?')) return;
                    axios.post('{{ route('admin.cms.audit_logs.clear') }}')
                        .then(res => {
                            this.auditLogs = [];
                            toast(res.data.message || 'Audit logs cleared.', 'success');
                        })
                        .catch(err => {
                            toast('Error clearing audit logs', 'error');
                        });
                },

                // News Methods
                createNews() {
                    if (!this.newNews.title || !this.newNews.content) { toast('Title and Content required', 'error'); return; }
                    const formData = new FormData();
                    formData.append('title', this.newNews.title);
                    formData.append('summary', this.newNews.summary || '');
                    formData.append('content', this.newNews.content);
                    formData.append('published_at', this.newNews.published_at || '{{ date('Y-m-d') }}');
                    if (this.newNews.is_featured) formData.append('is_featured', '1');
                    const fileInput = document.getElementById('newNewsFileInput');
                    if (fileInput && fileInput.files[0]) {
                        formData.append('image', fileInput.files[0]);
                    }

                    axios.post('{{ route('admin.cms.news.store') }}', formData)
                        .then(res => {
                            if (res.data.news) this.newsList.unshift(res.data.news);
                            this.showNewsModal = false;
                            this.newNews = { title: '', summary: '', content: '', published_at: '{{ date('Y-m-d') }}', is_featured: false };
                            const fileInput = document.getElementById('newNewsFileInput');
                            if (fileInput) fileInput.value = '';
                            toast(res.data.message || 'News article published!', 'success');
                        })
                        .catch(err => toast(err.response?.data?.message || 'Error publishing news', 'error'));
                },
                openEditNews(n) {
                    this.editNewsData = JSON.parse(JSON.stringify(n));
                    this.showEditNewsModal = true;
                },
                updateNews() {
                    if (!this.editNewsData.title || !this.editNewsData.content) { toast('Title and Content required', 'error'); return; }
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('title', this.editNewsData.title);
                    formData.append('summary', this.editNewsData.summary || '');
                    formData.append('content', this.editNewsData.content);
                    formData.append('published_at', this.editNewsData.published_at);
                    if (this.editNewsData.is_featured) formData.append('is_featured', '1');
                    const fileInput = document.getElementById('editNewsFileInput');
                    if (fileInput && fileInput.files[0]) {
                        formData.append('image', fileInput.files[0]);
                    }

                    axios.post('{{ url('/admin/cms/news') }}/' + this.editNewsData.id, formData)
                        .then(res => {
                            if (res.data.news) {
                                const idx = this.newsList.findIndex(n => n.id === this.editNewsData.id);
                                if (idx !== -1) this.newsList[idx] = res.data.news;
                            }
                            this.showEditNewsModal = false;
                            const fileInput = document.getElementById('editNewsFileInput');
                            if (fileInput) fileInput.value = '';
                            toast(res.data.message || 'News article updated!', 'success');
                        })
                        .catch(err => toast(err.response?.data?.message || 'Error updating news', 'error'));
                },
                toggleFeaturedNews(n) {
                    axios.post('{{ url('/admin/cms/news') }}/' + n.id + '/featured')
                        .then(res => {
                            n.is_featured = res.data.is_featured;
                            toast(res.data.message, 'success');
                        })
                        .catch(err => toast('Error updating featured state', 'error'));
                },
                deleteNewsConfirmed() {
                    if (this.selectedNews) {
                        axios.delete('{{ url('/admin/cms/news') }}/' + this.selectedNews.id)
                            .then(res => {
                                this.newsList = this.newsList.filter(n => n.id !== this.selectedNews.id);
                                toast(res.data.message || 'News deleted!', 'success');
                            })
                            .catch(err => toast('Error deleting news', 'error'));
                    }
                    this.showDeleteNewsModal = false;
                    this.selectedNews = null;
                },
                // Contact Methods
                saveContactSettings() {
                    axios.post('{{ route('admin.cms.contact.settings') }}', this.contactSettings)
                        .then(res => toast(res.data.message || 'Contact settings updated!', 'success'))
                        .catch(err => toast('Error saving contact settings', 'error'));
                },
                toggleReadContact(c) {
                    axios.post('{{ url('/admin/cms/contact/messages') }}/' + c.id + '/read')
                        .then(res => {
                            c.is_read = res.data.is_read;
                            toast(res.data.message, 'success');
                        })
                        .catch(err => toast('Error updating message status', 'error'));
                },
                deleteContact(c) {
                    if (!confirm('Delete this contact message permanently?')) return;
                    axios.delete('{{ url('/admin/cms/contact/messages') }}/' + c.id)
                        .then(res => {
                            this.contactMessages = this.contactMessages.filter(item => item.id !== c.id);
                            if (this.selectedContact && this.selectedContact.id === c.id) {
                                this.showContactModal = false;
                                this.selectedContact = null;
                            }
                            toast(res.data.message || 'Message deleted', 'success');
                        })
                        .catch(err => toast('Error deleting message', 'error'));
                },
                // Category Methods
                saveProgrammeCategories() {
                    axios.post('{{ route('admin.cms.programmes.categories') }}', {
                        title: this.catalogHeader.title,
                        subtitle: this.catalogHeader.subtitle,
                        categories: JSON.stringify(this.programmeCategories)
                    })
                    .then(res => {
                        toast(res.data.message || 'Academic programme categories published!', 'success');
                    })
                    .catch(err => toast('Error updating programme categories', 'error'));
                },
                toggleCategoryActive(cat) {
                    cat.is_active = !cat.is_active;
                    this.saveProgrammeCategories();
                },
                openAddCategory() {
                    this.newCategory = { code: 'DIP', title: '', subtitle: '', color: 'blue', is_active: true };
                    this.showInlineAddCategory = !this.showInlineAddCategory;
                    this.showAddCategoryModal = true;
                },
                addCategory() {
                    if (!this.newCategory.code || !this.newCategory.title) { toast('Code & Title required', 'error'); return; }
                    this.programmeCategories.push({
                        id: Date.now(),
                        code: this.newCategory.code,
                        title: this.newCategory.title,
                        subtitle: this.newCategory.subtitle || '',
                        color: this.newCategory.color || 'blue',
                        is_active: this.newCategory.is_active !== undefined ? !!this.newCategory.is_active : true
                    });
                    this.showAddCategoryModal = false;
                    this.showInlineAddCategory = false;
                    this.saveProgrammeCategories();
                    this.newCategory = { code: 'DIP', title: '', subtitle: '', color: 'blue', is_active: true };
                },
                openEditCategory(cat, idx) {
                    this.editCategoryData = {
                        idx: idx,
                        id: cat.id || Date.now(),
                        code: cat.code || '',
                        title: cat.title || '',
                        subtitle: cat.subtitle || '',
                        color: cat.color || 'blue',
                        is_active: cat.is_active !== undefined ? !!cat.is_active : true
                    };
                    this.editingCategoryIdx = idx;
                    this.inlineCategoryData = { ...this.editCategoryData };
                    this.showEditCategoryModal = true;
                },
                saveInlineCategoryEdit(idx) {
                    if (this.programmeCategories[idx] !== undefined) {
                        this.programmeCategories[idx] = {
                            id: this.inlineCategoryData.id || Date.now(),
                            code: this.inlineCategoryData.code,
                            title: this.inlineCategoryData.title,
                            subtitle: this.inlineCategoryData.subtitle,
                            color: this.inlineCategoryData.color,
                            is_active: !!this.inlineCategoryData.is_active
                        };
                        this.saveProgrammeCategories();
                    }
                    this.editingCategoryIdx = null;
                },
                cancelInlineCategoryEdit() {
                    this.editingCategoryIdx = null;
                },
                updateCategory() {
                    if (this.editCategoryData.idx !== null && this.programmeCategories[this.editCategoryData.idx] !== undefined) {
                        this.programmeCategories[this.editCategoryData.idx] = {
                            id: this.editCategoryData.id || Date.now(),
                            code: this.editCategoryData.code,
                            title: this.editCategoryData.title,
                            subtitle: this.editCategoryData.subtitle,
                            color: this.editCategoryData.color,
                            is_active: !!this.editCategoryData.is_active
                        };
                        this.saveProgrammeCategories();
                    }
                    this.showEditCategoryModal = false;
                    this.editingCategoryIdx = null;
                },
                deleteCategory(idx) {
                    if (!confirm('Are you sure you want to remove this academic category from the mega menu?')) return;
                    this.programmeCategories.splice(idx, 1);
                    this.saveProgrammeCategories();
                }
            }));
        });
    </script>
</x-app-layout>
