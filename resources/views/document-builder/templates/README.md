# Document Builder - Modular Template Architecture

## Overview

The Document Builder has been **fully modularized** to make template management easier and more maintainable. Each template type now has its own dedicated form files, allowing you to modify individual templates without affecting others.

---

## 📁 Directory Structure

```
resources/views/document-builder/
├── document-builder.blade.php          # Main entry point
├── partials/
│   ├── template-form-router.blade.php  # Routes to correct template form
│   ├── context-panel.blade.php         # Supplementary content area
│   ├── header.blade.php                # Page header
│   ├── config-panel/
│   │   └── brand-select.blade.php      # Brand selection (shared)
│   └── preview-panel/
│       ├── tabs.blade.php
│       ├── preview-tab.blade.php
│       ├── html-tab.blade.php
│       ├── loading-overlay.blade.php
│       └── tailoring-insight.blade.php
└── templates/                          # ⭐ NEW: Template-specific forms
    ├── cv-resume/
    │   ├── cv-classic.blade.php        # Classic Professional CV
    │   ├── cv-modern.blade.php         # Modern Creative CV
    │   ├── cv-technical.blade.php      # Technical Expert CV
    │   └── cv-international.blade.php  # International Standard CV
    ├── cover-letter/
    │   ├── cl-standard.blade.php       # Standard Professional
    │   └── cl-creative.blade.php       # Modern Creative
    ├── proposal/
    │   ├── proposal-standard.blade.php # Standard Business Proposal
    │   └── proposal-modern.blade.php   # Modern Pitch
    ├── contract/
    │   ├── contract-service.blade.php  # Service Agreement
    │   ├── contract-nda.blade.php      # Non-Disclosure Agreement
    │   ├── contract-employment.blade.php # Employment Contract
    │   └── contract-freelance.blade.php # Freelance Agreement
    └── reports/
        └── shared-form.blade.php       # Shared form for all report types
```

---

## 🎯 How It Works

### 1. Template Selection
User selects a template category (e.g., CV/Resume) from the template grid.

### 2. Dynamic Form Loading
The `template-form-router.blade.php` component detects the selected template and variant, then dynamically loads the appropriate form:

```blade
<template x-if="template === 'cv-resume'">
    <div>
        <template x-if="templateVariant === 'cv-classic'">
            <div>@include('document-builder.templates.cv-resume.cv-classic')</div>
        </template>
        <!-- Other variants... -->
    </div>
</template>
```

### 3. Template-Specific Fields
Each template has its own custom fields optimized for that document type:

- **CV Classic**: ATS-friendly, text-focused fields
- **CV Modern**: Visual elements, portfolio URL emphasis
- **CV Technical**: Tech stack, GitHub, project history
- **CV International**: Personal details (age, nationality, etc.)
- **Contracts**: Legal parties, terms, payment schedules
- **Reports**: Analysis focus, data sources, metrics

---

## ✏️ How to Edit Templates

### Example: Modifying the Modern CV Template

**File:** `resources/views/document-builder/templates/cv-resume/cv-modern.blade.php`

```blade
{{-- Add a new field for LinkedIn --}}
<div>
    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
        LinkedIn Profile
    </label>
    <input 
        type="url" 
        x-model="personalInfo.linkedin"
        placeholder="linkedin.com/in/yourprofile"
        class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm"
    >
</div>
```

**That's it!** Changes only affect the Modern CV variant. Other CV templates remain untouched.

---

## 🆕 Adding a New Template Variant

### Step 1: Create the Form File

Create a new blade file in the appropriate directory:

```bash
touch resources/views/document-builder/templates/cv-resume/cv-executive.blade.php
```

### Step 2: Design Your Form

```blade
{{--
    CV/Resume Template: Executive Profile
    
    Premium layout for C-level executives
--}}

<div class="space-y-6">
    <div>
        <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-3">
            Executive Title
        </label>
        <input 
            type="text" 
            x-model="targetRole"
            placeholder="e.g., Chief Technology Officer"
            class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm"
        >
    </div>
    
    {{-- Add more executive-specific fields --}}
</div>
```

### Step 3: Register in Router

Edit `resources/views/document-builder/partials/template-form-router.blade.php`:

```blade
<template x-if="template === 'cv-resume'">
    <div>
        <!-- Existing variants... -->
        
        <template x-if="templateVariant === 'cv-executive'">
            <div>@include('document-builder.templates.cv-resume.cv-executive')</div>
        </template>
    </div>
</template>
```

### Step 4: Register Variant in Enum

Edit `app/Enums/ReportTemplate.php` and add your new variant to the `variants()` method:

```php
case CV_RESUME => [
    // Existing variants...
    ['id' => 'cv-executive', 'name' => 'Executive Profile', 'description' => 'Premium C-level resume', 'previewImage' => 'cv', 'tags' => ['Executive', 'Premium']],
],
```

**Done!** Your new variant will appear in the UI.

---

## 🔄 Alpine.js Data Access

All template forms have access to the parent Alpine.js component's data:

### Available Properties

```javascript
// Template Selection
template          // 'cv-resume', 'cover-letter', etc.
templateVariant   // 'cv-classic', 'cl-standard', etc.

// Identity
senderName        // Your name
senderTitle       // Your title
recipientName     // Client/company name
recipientTitle    // Client contact or role

// CV/Resume Specific
targetRole        // Job position
profilePhotoUrl   // Uploaded photo URL
email, phone, location, website
personalInfo      // Object: age, dob, gender, nationality, etc.

// Financial (Proposals/Contracts)
financials        // Object: totalInvestment, currency, timeline, paymentMilestones

// Contract Specific
contractDetails   // Object: addresses, dates, duration, tax IDs

// Content
sourceContent     // Main content textarea
prompt            // Title/instructions
researchTopic     // Report topic
analysisType      // Analysis focus/objective

// UI State
isGenerating      // Currently generating document
isParsing         // Parsing uploaded file
isUploadingPhoto  // Uploading photo
```

### Available Methods

```javascript
uploadPhoto(event)      // Handle photo upload
parseResume(event)      // Parse PDF/DOCX resume
draftCoverLetter()      // AI draft cover letter
fetchPreview()          // Refresh preview (debounced)
generateReport()        // Generate final document
```

---

## 🎨 Styling Guidelines

### Input Fields
```blade
<input 
    type="text" 
    x-model="fieldName"
    placeholder="Helpful placeholder"
    class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
>
```

### Textareas
```blade
<textarea 
    x-model="content"
    rows="8"
    placeholder="Enter content..."
    class="w-full px-5 py-4 rounded-2xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all leading-relaxed"
></textarea>
```

### Section Headers
```blade
<h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">
    Section Title
</h4>
```

### Grouped Sections
```blade
<div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4">
    <h4 class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Section</h4>
    {{-- Fields --}}
</div>
```

---

## 🧪 Testing Your Changes

### 1. Select Your Template
Navigate to the Document Builder and select your template from the grid.

### 2. Verify Form Loads
Ensure your custom form fields appear correctly.

### 3. Test Data Binding
Fill out the form and verify that:
- Data persists when switching variants
- Preview updates when fields change (debounced)
- File uploads work correctly

### 4. Generate Document
Click "Generate Report" and verify the AI receives the correct data.

---

## 🚀 Performance Notes

### Debouncing
Preview requests are **debounced by 300ms** to prevent API spam during rapid typing.

### Abort Controller
In-flight preview requests are **automatically cancelled** when a new request is initiated.

### Lazy Loading
Forms are loaded **only when their template is selected**, keeping the initial page load fast.

---

## 📚 Related Files

### Backend
- `app/Http/Controllers/DocumentBuilderController.php` - Main controller
- `app/Enums/ReportTemplate.php` - Template definitions & variants
- `app/Services/ReportService.php` - AI generation logic

### Frontend
- `resources/js/document-builder/` - Alpine.js modules (if extracted)
- `resources/views/components/template-selector.blade.php` - Template grid

---

## 🐛 Troubleshooting

### Form Not Appearing
- Check template name matches exactly in `template-form-router.blade.php`
- Verify Alpine.js `x-if` conditions are correct
- Clear browser cache and reload

### Fields Not Saving
- Ensure `x-model` binds to correct Alpine property
- Check Alpine component has the property initialized
- Inspect browser console for JavaScript errors

### Preview Not Updating
- Verify `fetchPreview()` is called on field change
- Check debounce timer is working (300ms delay)
- Review network tab for failed API calls

---

## 📖 Best Practices

### ✅ DO
- Create descriptive file names (`cv-international.blade.php`)
- Add comments explaining template purpose
- Use consistent styling classes
- Test with real data before committing
- Document any new Alpine properties

### ❌ DON'T
- Hardcode values - use Alpine data binding
- Mix template logic in the main file
- Create fields without proper labels
- Forget to add validation hints
- Skip testing on mobile viewport

---

## 🎉 Benefits of This Architecture

### 🔧 **Easy Maintenance**
Modify one template without touching others. No more merge conflicts!

### 📦 **Better Organization**
Each template lives in its own file with clear naming.

### 🚀 **Faster Development**
Add new templates by copying an existing one and customizing.

### 🧪 **Isolated Testing**
Test changes to one template in isolation.

### 📱 **Team Collaboration**
Multiple developers can work on different templates simultaneously.

### 🔍 **Better Code Review**
Changes are localized to specific files, making PRs cleaner.

---

## 📝 Template Checklist

When creating a new template:

- [ ] Created form file in correct directory
- [ ] Added descriptive header comment
- [ ] Used consistent styling classes
- [ ] Bound fields to appropriate Alpine properties
- [ ] Added helpful placeholders
- [ ] Registered in template router
- [ ] Added variant to enum (if new variant)
- [ ] Tested data binding
- [ ] Tested preview generation
- [ ] Tested final document generation
- [ ] Documented any new Alpine properties
- [ ] Mobile responsiveness verified

---

## 🤝 Contributing

When contributing new templates or modifications:

1. **Follow the existing structure** - Keep files organized
2. **Test thoroughly** - Verify all functionality works
3. **Document changes** - Update this README if needed
4. **Use semantic naming** - Clear, descriptive file names
5. **Keep it DRY** - Extract shared components when appropriate

---

## 📞 Support

For questions or issues with the modular template system:

- Check this README first
- Review existing template files for examples
- Inspect browser console for errors
- Contact the development team

---

**Last Updated:** February 2026  
**Maintainer:** Architect AI Team
