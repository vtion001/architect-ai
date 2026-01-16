{{-- Recipient / Candidate Identity --}}
{{-- 
    Expects parent x-data with: 
    template, recipientName, recipientTitle, companyAddress, contractDetails,
    profilePhotoUrl, isUploadingPhoto, uploadPhoto(),
    email, phone, location, website, personalInfo, fetchPreview()
--}}
<div class="pt-6 border-t border-border/50">
    <div class="flex items-center justify-between mb-4">
        <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1" 
               x-text="template === 'cv-resume' ? 'Candidate Identity' : (template === 'cover-letter' ? 'Target Company Details' : 'Identity Destination')"></label>
        
        {{-- Photo Upload (CV Only) --}}
        <div x-show="template === 'cv-resume'" class="relative">
            <input type="file" id="photoUpload" class="hidden" accept="image/*" @change="uploadPhoto">
            <label for="photoUpload" class="cursor-pointer flex items-center gap-2 text-[9px] font-bold text-muted-foreground hover:text-primary transition-colors">
                <template x-if="!profilePhotoUrl && !isUploadingPhoto">
                    <span class="flex items-center gap-1"><i data-lucide="camera" class="w-3 h-3"></i> Add Photo</span>
                </template>
                <template x-if="isUploadingPhoto">
                    <span class="flex items-center gap-1"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i> Uploading...</span>
                </template>
                <template x-if="profilePhotoUrl">
                    <div class="flex items-center gap-2">
                        <img :src="profilePhotoUrl" class="w-6 h-6 rounded-full object-cover border border-border">
                        <span class="text-green-600">Change</span>
                    </div>
                </template>
            </label>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4">
        <input x-model="recipientName" @input.debounce.800ms="fetchPreview" type="text" 
               :placeholder="template === 'cv-resume' ? 'Full Name' : (template === 'cover-letter' ? 'Hiring Manager Name' : 'Recipient Name')"
               class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
        <input x-model="recipientTitle" @input.debounce.800ms="fetchPreview" type="text" 
               :placeholder="template === 'cv-resume' ? 'Professional Title (e.g. Senior Architect)' : (template === 'cover-letter' ? 'Company Name' : 'Identity Role (e.g. CEO)')"
               class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">

        {{-- Contract Specific Client Details --}}
        <div x-show="template === 'contract'" class="space-y-4 pt-2" x-transition>
            <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Client Legal Details</label>
            <input x-model="contractDetails.clientAddress" @input.debounce.800ms="fetchPreview" type="text" placeholder="Client Street Address"
                   class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
            
            <div class="grid grid-cols-2 gap-4">
                <input x-model="contractDetails.clientCity" @input.debounce.800ms="fetchPreview" type="text" placeholder="City, State, Zip"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                <input x-model="contractDetails.clientCountry" @input.debounce.800ms="fetchPreview" type="text" placeholder="Country"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <input x-model="contractDetails.clientEmail" @input.debounce.800ms="fetchPreview" type="email" placeholder="Client Email"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                <input x-model="contractDetails.clientTaxId" @input.debounce.800ms="fetchPreview" type="text" placeholder="Client Tax ID / EIN"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
            </div>
        </div>
        
        {{-- Cover Letter Company Address --}}
        <div x-show="template === 'cover-letter'" x-transition>
            <input x-model="companyAddress" type="text" placeholder="Company Address"
                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
        </div>
        
        {{-- CV Specific Contact Info --}}
        <div x-show="template === 'cv-resume'" x-transition class="space-y-4 pt-2">
            <div class="grid grid-cols-2 gap-4">
                <input x-model="email" type="email" placeholder="Email Address"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                <input x-model="phone" type="text" placeholder="Phone Number"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <input x-model="location" type="text" placeholder="Location (City, Country)"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                <input x-model="website" type="text" placeholder="Portfolio / LinkedIn"
                       class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
            </div>
            
            {{-- Extended Bio Data --}}
            <div class="pt-4 border-t border-border/50">
                <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground mb-3 block">Professional Information (Bio-Data)</label>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input x-model="personalInfo.age" type="text" placeholder="Age" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                    <input x-model="personalInfo.dob" type="text" placeholder="Date of Birth" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input x-model="personalInfo.gender" type="text" placeholder="Gender" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                    <input x-model="personalInfo.civil_status" type="text" placeholder="Civil Status" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input x-model="personalInfo.height" type="text" placeholder="Height" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                    <input x-model="personalInfo.weight" type="text" placeholder="Weight" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input x-model="personalInfo.nationality" type="text" placeholder="Nationality" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                    <input x-model="personalInfo.religion" type="text" placeholder="Religion" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                </div>
                <div class="space-y-3">
                    <input x-model="personalInfo.place_of_birth" type="text" placeholder="Place of Birth" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                    <input x-model="personalInfo.languages" type="text" placeholder="Languages / Dialects" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                </div>
            </div>
        </div>
    </div>
</div>
