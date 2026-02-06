# Video Styles - Quick Reference Index

## 📁 File Structure
```
video-styles/
├── VIDEO-MODULARIZATION-GUIDE.md  # Complete documentation (you're here!)
├── ugc-authentic.blade.php        # UGC/Authentic style form
├── cinematic.blade.php            # Cinematic style form
├── animation.blade.php            # 3D Animation style form
└── minimalist.blade.php           # Minimalist style form
```

## 🎬 Style Quick Reference

### UGC / Authentic
- **File**: `ugc-authentic.blade.php`
- **Theme**: Orange/Amber 🟠
- **Icon**: smartphone
- **Use Cases**: Testimonials, unboxings, tutorials, authentic content
- **Key Features**: Natural imperfections, casual framing, handheld camera
- **Parameters**: 10 unique (scenario, environment, camera movement, authenticity toggles)

### Cinematic
- **File**: `cinematic.blade.php`
- **Theme**: Purple/Indigo 🟣
- **Icon**: film
- **Use Cases**: Brand films, luxury products, emotional storytelling
- **Key Features**: Dramatic lighting, color grading, professional camera movements
- **Parameters**: 18 unique (mood, lighting, movement, color grading, effects)

### 3D Animation
- **File**: `animation.blade.php`
- **Theme**: Cyan/Blue 🔵
- **Icon**: box
- **Use Cases**: Product demos, explainers, abstract concepts
- **Key Features**: CGI rendering, physics simulation, material customization
- **Parameters**: 20+ unique (style, environment, animation, materials, physics, speed)

### Minimalist
- **File**: `minimalist.blade.php`
- **Theme**: Slate/Gray ⚫
- **Icon**: minus-circle
- **Use Cases**: Tech products, modern brands, B2B, clarity-focused
- **Key Features**: Clean backgrounds, limited colors, intentional white space
- **Parameters**: 15 unique (approach, background, typography, layout, composition)

---

## 🔄 Component Relationships

```
video-generator.blade.php (Main)
    ↓
    Includes: video-style-router.blade.php (Router)
        ↓
        Conditionally Includes (based on videoStyle):
            ├── ugc-authentic.blade.php
            ├── cinematic.blade.php
            ├── animation.blade.php
            └── minimalist.blade.php
```

---

## 🎛️ State Variables by Style

### UGC (6 variables)
```javascript
ugcScenario, environment, cameraMovement,
addImperfections, includeNoise, casualFraming
```

### Cinematic (8 variables)
```javascript
cinematicMood, lightingSetup, colorGrading,
lensFlares, filmGrain, depthOfField, motionBlur, anamorphic
```

### 3D Animation (11 variables)
```javascript
animationStyle, sceneEnvironment, cameraAnimation, material,
rayTracing, globalIllumination, particleEffects, physicsSimulation,
caustics, animationSpeed, colorPalette
```

### Minimalist (11 variables)
```javascript
visualApproach, backgroundStyle, typographyFocus, animationType, colorScheme,
centeredComposition, gridGuides, whiteSpace, sansSerifOnly, elementCount, layout
```

**Total**: 36 style-specific parameters + 7 common parameters = **43 total video parameters**

---

## 📝 Editing Guide

### Adding a New Video Style

1. **Create Style File**: `video-styles/your-style.blade.php`
2. **Follow Template Structure**:
   ```blade
   {{-- Style Description Box --}}
   <div class="bg-gradient-to-r from-color-50 to-color-50 rounded-xl p-5 border border-color-200">
       <!-- Icon + Title + Description -->
   </div>

   {{-- Style Parameters --}}
   <div class="space-y-4">
       <!-- Dropdowns, checkboxes, sliders -->
   </div>

   {{-- Advanced Options --}}
   <div class="bg-muted/30 rounded-xl p-5 space-y-4">
       <!-- Advanced settings -->
   </div>

   {{-- Tips Section --}}
   <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
       <!-- Best practices tips -->
   </div>
   ```

3. **Add to Router**: Update `video-style-router.blade.php`
   ```blade
   <div x-show="videoStyle === 'YourStyle'" x-transition>
       @include('content-creator.partials.generators.video-styles.your-style')
   </div>
   ```

4. **Add State Variables**: Update `content-creator.js`
   ```javascript
   // Video specific - Your Style
   yourParam1: 'default',
   yourParam2: false,
   ```

5. **Add Style Button**: Update `video-generator.blade.php`
   ```blade
   <button @click="videoStyle = 'YourStyle'" 
           :class="videoStyle === 'YourStyle' ? 'bg-color-100 border-color-400' : '...'"
           class="h-20 border-2 rounded-xl flex flex-col items-center justify-center">
       <i data-lucide="icon-name" class="w-5 h-5"></i>
       <span class="text-xs font-bold">Your Style</span>
   </button>
   ```

### Modifying Existing Style

1. **Locate Style File**: `video-styles/[style-name].blade.php`
2. **Add Parameter**: Insert new form field (dropdown, checkbox, slider)
3. **Add State Variable**: Add to `content-creator.js`
4. **Update Backend**: Add parameter to controller handling
5. **Test**: Verify parameter saves and submits correctly

---

## 🧪 Testing Checklist

### For Each Style:
- [ ] Style button changes active state correctly
- [ ] Style-specific form appears when selected
- [ ] All dropdowns have values
- [ ] Checkboxes toggle properly
- [ ] Sliders update displayed value
- [ ] Transitions are smooth between styles
- [ ] Parameters persist when switching styles
- [ ] Tips section displays correctly
- [ ] Form submits all parameters
- [ ] Backend receives style-specific params

---

## 🎨 Design System

### Color Themes by Style:
- **UGC**: `orange-50, orange-100, orange-200, orange-600, orange-700, orange-900`
- **Cinematic**: `purple-50, purple-100, purple-200, purple-400, purple-600, purple-700, purple-900`
- **Animation**: `cyan-50, cyan-100, cyan-200, cyan-400, cyan-600, cyan-700, cyan-900`
- **Minimalist**: `slate-50, slate-100, slate-200, slate-400, slate-600, slate-700, slate-900`

### Icon Mapping:
- UGC: `smartphone`
- Cinematic: `film`
- Animation: `box`
- Minimalist: `minus-circle`

### Common Icons:
- Settings: `sliders-horizontal`
- Tips: `lightbulb`
- Layout: `layout`
- Effects: `sparkles`, `clapperboard`, `layers`

---

## 📊 Parameter Counts

| Style | Dropdowns | Checkboxes | Sliders | Color Selectors | Total Params |
|-------|-----------|------------|---------|-----------------|--------------|
| UGC | 3 | 3 | 0 | 0 | **6** |
| Cinematic | 4 | 5 | 0 | 0 | **9** |
| Animation | 4 | 5 | 1 | 1 | **11** |
| Minimalist | 4 | 4 | 2 | 2 | **12** |
| **TOTAL** | **15** | **17** | **3** | **3** | **38** |

---

## 🔗 Related Files

### Frontend:
- **Main Interface**: `../video-generator.blade.php`
- **Router**: `../video-style-router.blade.php`
- **Alpine Component**: `../../../../js/components/content-creator.js`

### Backend (To Be Implemented):
- **Controller**: `app/Http/Controllers/ContentCreatorController.php`
- **Service**: `app/Services/VideoGenerationService.php`
- **Job**: `app/Jobs/GenerateVideo.php`

---

## 📚 Documentation

- **Complete Guide**: [VIDEO-MODULARIZATION-GUIDE.md](VIDEO-MODULARIZATION-GUIDE.md)
- **Content Creator Main**: [../../README.md](../../README.md)
- **System Architecture**: [../../../../architect-ai-docs/03-Services.md](../../../../architect-ai-docs/03-Services.md)

---

**Quick Tips**:
- Each style file is ~150-250 lines
- Use `x-show` (not `x-if`) for router to maintain state
- Always include tips section for user education
- Follow color theme consistency for visual coherence
- Test parameter submission with browser DevTools

**Need Help?**  
Refer to [VIDEO-MODULARIZATION-GUIDE.md](VIDEO-MODULARIZATION-GUIDE.md) for complete details.
