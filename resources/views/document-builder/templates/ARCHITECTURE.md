# Document Builder Architecture Diagram

## 📐 System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    Document Builder Entry                       │
│                 (document-builder.blade.php)                    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐  │
│  │         Alpine.js Component State                       │  │
│  │  • template, templateVariant                            │  │
│  │  • senderName, recipientName                            │  │
│  │  • sourceContent, prompt                                │  │
│  │  • financials, contractDetails                          │  │
│  │  • Methods: generateReport(), fetchPreview()            │  │
│  └─────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
         ┌────────────────────────────────────────┐
         │     Configuration Panel                │
         │  ┌──────────────────────────────────┐  │
         │  │  Brand Selection (Shared)        │  │
         │  └──────────────────────────────────┘  │
         │               │                         │
         │               ▼                         │
         │  ┌──────────────────────────────────┐  │
         │  │  Template Form Router            │  │
         │  │  (Dynamic Component Loader)      │  │
         │  └──────────────────────────────────┘  │
         └────────────────────────────────────────┘
                              │
                ┌─────────────┴──────────────┐
                │                            │
                ▼                            ▼
    ┌───────────────────────┐   ┌───────────────────────┐
    │   Template Type?      │   │   Variant Selected?   │
    │  • cv-resume          │   │  • cv-classic         │
    │  • cover-letter       │   │  • cv-modern          │
    │  • proposal           │   │  • cv-technical       │
    │  • contract           │   │  • cv-international   │
    │  • reports            │   │  • (and others...)    │
    └───────────────────────┘   └───────────────────────┘
                │
                ▼
    ┌───────────────────────────────────────────────────┐
    │              Load Specific Form                   │
    └───────────────────────────────────────────────────┘
                              │
         ┌────────────────────┼────────────────────┐
         │                    │                    │
         ▼                    ▼                    ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│  CV/Resume      │  │  Cover Letter   │  │  Proposal       │
│  ┌───────────┐  │  │  ┌───────────┐  │  │  ┌───────────┐  │
│  │cv-classic │  │  │  │cl-standard│  │  │  │proposal-  │  │
│  └───────────┘  │  │  └───────────┘  │  │  │standard   │  │
│  ┌───────────┐  │  │  ┌───────────┐  │  │  └───────────┘  │
│  │cv-modern  │  │  │  │cl-creative│  │  │  ┌───────────┐  │
│  └───────────┘  │  │  └───────────┘  │  │  │proposal-  │  │
│  ┌───────────┐  │  │                 │  │  │modern     │  │
│  │cv-        │  │  │                 │  │  └───────────┘  │
│  │technical  │  │  │                 │  │                 │
│  └───────────┘  │  │                 │  │                 │
│  ┌───────────┐  │  │                 │  │                 │
│  │cv-        │  │  │                 │  │                 │
│  │intl       │  │  │                 │  │                 │
│  └───────────┘  │  │                 │  │                 │
└─────────────────┘  └─────────────────┘  └─────────────────┘

         ▼                    ▼                    ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│  Contract       │  │  Reports        │  │  Custom         │
│  ┌───────────┐  │  │  ┌───────────┐  │  │  ┌───────────┐  │
│  │contract-  │  │  │  │Executive  │  │  │  │Flexible   │  │
│  │service    │  │  │  │Summary    │  │  │  │Layout     │  │
│  └───────────┘  │  │  └───────────┘  │  │  └───────────┘  │
│  ┌───────────┐  │  │  ┌───────────┐  │  │                 │
│  │contract-  │  │  │  │Market     │  │  │                 │
│  │nda        │  │  │  │Analysis   │  │  │                 │
│  └───────────┘  │  │  └───────────┘  │  │                 │
│  ┌───────────┐  │  │  ┌───────────┐  │  │                 │
│  │contract-  │  │  │  │Financial  │  │  │                 │
│  │employment │  │  │  │Overview   │  │  │                 │
│  └───────────┘  │  │  └───────────┘  │  │                 │
│  ┌───────────┐  │  │  ┌───────────┐  │  │                 │
│  │contract-  │  │  │  │Competitive│  │  │                 │
│  │freelance  │  │  │  │Intel      │  │  │                 │
│  └───────────┘  │  │  └───────────┘  │  │                 │
│                 │  │  ┌───────────┐  │  │                 │
│                 │  │  │Trend      │  │  │                 │
│                 │  │  │Analysis   │  │  │                 │
│                 │  │  └───────────┘  │  │                 │
│                 │  │  ┌───────────┐  │  │                 │
│                 │  │  │Infographic│  │  │                 │
│                 │  │  └───────────┘  │  │                 │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

---

## 🔄 Data Flow

```
User Action (Select Template)
         │
         ▼
Alpine.js State Update
(template = 'cv-resume')
         │
         ▼
Template Router Evaluates Conditions
(x-if="template === 'cv-resume'")
         │
         ▼
Loads Appropriate Form Partial
(@include('templates/cv-resume/cv-classic'))
         │
         ▼
Form Fields Bind to Alpine State
(x-model="targetRole")
         │
         ▼
User Fills Form
         │
         ▼
Alpine Watchers Trigger
($watch('template', fetchPreview))
         │
         ▼
Debounced Preview Request (300ms)
         │
         ▼
Preview Updates
         │
         ▼
User Clicks "Generate"
         │
         ▼
generateReport() Method
         │
         ▼
POST to /document-builder/generate
         │
         ▼
Backend Processing (AI Generation)
         │
         ▼
Document Returned
```

---

## 📂 File Dependency Graph

```
document-builder.blade.php
    │
    ├── partials/header.blade.php
    │
    ├── partials/config-panel/
    │   └── brand-select.blade.php
    │
    ├── partials/template-form-router.blade.php  ← ROUTER
    │   │
    │   ├── templates/cv-resume/
    │   │   ├── cv-classic.blade.php
    │   │   ├── cv-modern.blade.php
    │   │   ├── cv-technical.blade.php
    │   │   └── cv-international.blade.php
    │   │
    │   ├── templates/cover-letter/
    │   │   ├── cl-standard.blade.php
    │   │   └── cl-creative.blade.php
    │   │
    │   ├── templates/proposal/
    │   │   ├── proposal-standard.blade.php
    │   │   └── proposal-modern.blade.php
    │   │
    │   ├── templates/contract/
    │   │   ├── contract-service.blade.php
    │   │   ├── contract-nda.blade.php
    │   │   ├── contract-employment.blade.php
    │   │   └── contract-freelance.blade.php
    │   │
    │   └── templates/reports/
    │       └── shared-form.blade.php
    │
    ├── partials/context-panel.blade.php
    │
    ├── components/template-selector.blade.php
    │
    └── partials/preview-panel/
        ├── tabs.blade.php
        ├── preview-tab.blade.php
        ├── html-tab.blade.php
        ├── loading-overlay.blade.php
        └── tailoring-insight.blade.php
```

---

## 🎯 Template Selection Flow

```
┌─────────────────────────────────────────────────────────┐
│ User sees template grid (12 template categories)        │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ User clicks "CV / Resume" template                      │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ Alpine updates: template = 'cv-resume'                  │
│ Alpine updates: templateVariant = 'cv-classic' (default)│
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ Router evaluates: x-if="template === 'cv-resume'"      │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ Router evaluates: x-if="templateVariant === 'cv-classic'│
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ Loads: templates/cv-resume/cv-classic.blade.php        │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ Form fields render with Alpine data bindings           │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ User can now fill out CV-specific form fields          │
└─────────────────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ User clicks variant selector (e.g., "Modern Creative") │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ Alpine updates: templateVariant = 'cv-modern'          │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ Router swaps to: templates/cv-resume/cv-modern.blade.php│
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ New form fields render (data persists via Alpine)      │
└─────────────────────────────────────────────────────────┘
```

---

## 🧩 Component Interaction

```
┌────────────────────────────────────────────────────────────┐
│                 Alpine.js Component                        │
│  (document-builder.blade.php)                             │
│  ┌──────────────────────────────────────────────────────┐ │
│  │ Data Properties                                      │ │
│  │ • template: 'cv-resume'                              │ │
│  │ • templateVariant: 'cv-classic'                      │ │
│  │ • sourceContent: '...'                               │ │
│  │ • All form fields accessible                         │ │
│  └──────────────────────────────────────────────────────┘ │
│  ┌──────────────────────────────────────────────────────┐ │
│  │ Methods                                              │ │
│  │ • fetchPreview() - Debounced 300ms                   │ │
│  │ • generateReport() - POST request                    │ │
│  │ • uploadPhoto() - File handling                      │ │
│  │ • parseResume() - AI extraction                      │ │
│  └──────────────────────────────────────────────────────┘ │
│  ┌──────────────────────────────────────────────────────┐ │
│  │ Watchers                                             │ │
│  │ • $watch('template') → fetchPreview()                │ │
│  │ • $watch('templateVariant') → fetchPreview()         │ │
│  │ • $watch('selectedBrandId') → fetchPreview()         │ │
│  └──────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────┘
                           │
                           │ Data & Methods Available To:
                           │
                           ▼
┌────────────────────────────────────────────────────────────┐
│          Template Form Files (Child Components)            │
│  ┌──────────────────────────────────────────────────────┐ │
│  │ cv-classic.blade.php                                 │ │
│  │ <input x-model="targetRole">  ← Binds to parent      │ │
│  │ <input x-model="email">       ← Binds to parent      │ │
│  │ @change="uploadPhoto"         ← Calls parent method  │ │
│  └──────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────┘
```

---

## 📊 Template Coverage

```
Total Templates: 12
├── CV/Resume: 4 variants (33.3%)
│   ├── Classic Professional
│   ├── Modern Creative
│   ├── Technical Expert
│   └── International Standard
│
├── Cover Letter: 2 variants (16.7%)
│   ├── Standard Professional
│   └── Modern Creative
│
├── Proposal: 2 variants (16.7%)
│   ├── Standard Business
│   └── Modern Pitch
│
├── Contract: 4 variants (33.3%)
│   ├── Service Agreement
│   ├── NDA
│   ├── Employment Contract
│   └── Freelance Agreement
│
└── Reports: 6 types share 1 form (shared)
    ├── Executive Summary
    ├── Market Analysis
    ├── Financial Overview
    ├── Competitive Intelligence
    ├── Trend Analysis
    └── Infographic

Total Variant Files: 13
Total Form Files: 13 + 1 shared = 14
```

---

## 🎨 Styling Consistency

All template forms follow the same design system:

```
Input Fields
────────────────────────────────────
Class: w-full px-5 py-3.5 rounded-2xl border
       border-slate-200 text-sm
       focus:ring-2 focus:ring-primary/20
Color Scheme: Slate + Primary accent
Border Radius: 2xl (20px)
Padding: 14px vertical, 20px horizontal

Labels
────────────────────────────────────
Class: text-[10px] font-bold text-slate-600
       uppercase tracking-wider
Size: 10px
Weight: Bold (700)
Spacing: 0.2em letter-spacing

Sections
────────────────────────────────────
Container: bg-slate-50 rounded-2xl p-6
           border border-slate-200
Header: text-[10px] font-bold text-slate-700
        uppercase tracking-wider
```

---

**Architecture Documented:** February 2026  
**Diagram Version:** 1.0  
**Status:** ✅ Current
