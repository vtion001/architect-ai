<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpCenterController extends Controller
{
    /**
     * Display the help center index page.
     */
    public function index(): View
    {
        $sections = $this->getHelpSections();
        
        return view('help-center.help-center', [
            'sections' => $sections,
        ]);
    }

    /**
     * Display a specific help article.
     */
    public function show(string $section, string $article): View
    {
        $sections = $this->getHelpSections();
        
        // Find the section and article
        $sectionData = collect($sections)->firstWhere('slug', $section);
        $articleData = $sectionData 
            ? collect($sectionData['articles'])->firstWhere('slug', $article)
            : null;
        
        if (!$sectionData || !$articleData) {
            abort(404, 'Article not found');
        }
        
        return view('help-center.article', [
            'section' => $sectionData,
            'article' => $articleData,
            'allSections' => $sections,
        ]);
    }

    /**
     * Get all help center sections and articles.
     */
    private function getHelpSections(): array
    {
        return [
            [
                'title' => 'Getting Started',
                'slug' => 'getting-started',
                'icon' => 'rocket',
                'description' => 'Learn the basics and get up and running quickly',
                'articles' => [
                    [
                        'title' => 'Welcome to ArchitGrid',
                        'slug' => 'welcome',
                        'content' => $this->getWelcomeContent(),
                    ],
                    [
                        'title' => 'Dashboard Overview',
                        'slug' => 'dashboard-overview',
                        'content' => $this->getDashboardContent(),
                    ],
                ],
            ],
            [
                'title' => 'Task & Note Management',
                'slug' => 'tasks-notes',
                'icon' => 'check-square',
                'description' => 'Organize your work with intelligent task management',
                'articles' => [
                    [
                        'title' => 'Creating Tasks & Notes',
                        'slug' => 'creating-tasks',
                        'content' => $this->getTasksContent(),
                    ],
                    [
                        'title' => 'Voice Intelligence',
                        'slug' => 'voice-intelligence',
                        'content' => $this->getVoiceContent(),
                    ],
                    [
                        'title' => 'Task Breakdown',
                        'slug' => 'task-breakdown',
                        'content' => $this->getBreakdownContent(),
                    ],
                ],
            ],
            [
                'title' => 'Research Engine',
                'slug' => 'research-engine',
                'icon' => 'brain',
                'description' => 'Deep research and competitive intelligence gathering',
                'articles' => [
                    [
                        'title' => 'Starting Research Projects',
                        'slug' => 'starting-research',
                        'content' => $this->getResearchContent(),
                    ],
                ],
            ],
            [
                'title' => 'Content Creator',
                'slug' => 'content-creator',
                'icon' => 'pencil',
                'description' => 'Generate high-quality content for your brand',
                'articles' => [
                    [
                        'title' => 'Generating Content',
                        'slug' => 'generating-content',
                        'content' => $this->getContentCreatorContent(),
                    ],
                    [
                        'title' => 'AI Media Generation',
                        'slug' => 'media-generation',
                        'content' => $this->getMediaGenerationContent(),
                    ],
                ],
            ],
            [
                'title' => 'Social Planner',
                'slug' => 'social-planner',
                'icon' => 'calendar',
                'description' => 'Schedule and manage your social media presence',
                'articles' => [
                    [
                        'title' => 'Scheduling Posts',
                        'slug' => 'scheduling-posts',
                        'content' => $this->getSocialPlannerContent(),
                    ],
                ],
            ],
            [
                'title' => 'Knowledge Base',
                'slug' => 'knowledge-base',
                'icon' => 'database',
                'description' => 'Build your intelligent knowledge repository',
                'articles' => [
                    [
                        'title' => 'Managing Knowledge Assets',
                        'slug' => 'knowledge-assets',
                        'content' => $this->getKnowledgeBaseContent(),
                    ],
                ],
            ],
            [
                'title' => 'AI Agents',
                'slug' => 'ai-agents',
                'icon' => 'bot',
                'description' => 'Create and manage custom AI assistants',
                'articles' => [
                    [
                        'title' => 'Creating AI Agents',
                        'slug' => 'creating-agents',
                        'content' => $this->getAiAgentsContent(),
                    ],
                ],
            ],
            [
                'title' => 'Document Builder',
                'slug' => 'document-builder',
                'icon' => 'file-text',
                'description' => 'Professional document generation tools',
                'articles' => [
                    [
                        'title' => 'Building Documents',
                        'slug' => 'building-documents',
                        'content' => $this->getDocumentBuilderContent(),
                    ],
                ],
            ],
            [
                'title' => 'Media Registry',
                'slug' => 'media-registry',
                'icon' => 'image',
                'description' => 'Manage your media assets and library',
                'articles' => [
                    [
                        'title' => 'Managing Media',
                        'slug' => 'managing-media',
                        'content' => $this->getMediaRegistryContent(),
                    ],
                ],
            ],
            [
                'title' => 'Brand Kits',
                'slug' => 'brand-kits',
                'icon' => 'fingerprint',
                'description' => 'Manage your brand identity and assets',
                'articles' => [
                    [
                        'title' => 'Creating Brand Kits',
                        'slug' => 'creating-brands',
                        'content' => $this->getBrandKitsContent(),
                    ],
                ],
            ],
            [
                'title' => 'Team Management',
                'slug' => 'team-management',
                'icon' => 'users',
                'description' => 'Manage your team and access controls',
                'articles' => [
                    [
                        'title' => 'Inviting Team Members',
                        'slug' => 'inviting-members',
                        'content' => $this->getTeamManagementContent(),
                    ],
                    [
                        'title' => 'Access Policies',
                        'slug' => 'access-policies',
                        'content' => $this->getAccessPoliciesContent(),
                    ],
                ],
            ],
            [
                'title' => 'Analytics',
                'slug' => 'analytics',
                'icon' => 'bar-chart-3',
                'description' => 'Track performance and insights',
                'articles' => [
                    [
                        'title' => 'Understanding Analytics',
                        'slug' => 'understanding-analytics',
                        'content' => $this->getAnalyticsContent(),
                    ],
                ],
            ],
        ];
    }

    // Content methods for each article
    private function getWelcomeContent(): string
    {
        return <<<'MARKDOWN'
# Welcome to ArchitGrid

ArchitGrid is your all-in-one AI-powered workspace for content creation, research, team collaboration, and intelligent automation.

## What You Can Do

- **Create & Manage Tasks**: Organize your work with our intelligent task management system
- **Research**: Leverage AI for deep competitive intelligence and market research
- **Content Creation**: Generate high-quality content for blogs, social media, and more
- **Social Planning**: Schedule and manage your social media presence
- **Knowledge Management**: Build your intelligent knowledge repository
- **AI Agents**: Create custom AI assistants tailored to your needs
- **Document Building**: Generate professional documents with ease
- **Team Collaboration**: Manage your team and control access

## Getting Help

Navigate through the sections on the left to find detailed guides for each feature. If you need additional assistance, contact your account administrator.
MARKDOWN;
    }

    private function getDashboardContent(): string
    {
        return <<<'MARKDOWN'
# Dashboard Overview

Your dashboard is the central hub where you can:

## Quick Stats
- View your recent activity
- Monitor team performance
- Track content creation metrics

## Recent Items
- Access your latest tasks and notes
- View recent research projects
- See your newest content pieces

## Quick Actions
- Create new tasks from the floating widget
- Start a research project
- Generate content
- Schedule social posts

Navigate using the sidebar menu to access all platform features.
MARKDOWN;
    }

    private function getTasksContent(): string
    {
        return <<<'MARKDOWN'
# Creating Tasks & Notes

## How to Create a Task

1. Click the **floating widget** in the bottom-right corner
2. Select the **Tasks** tab
3. Enter your task title and description
4. Add any relevant details
5. Click **Save**

## Task Features

### Categories
Organize your tasks into custom categories for better organization.

### Priorities
Set task priorities to focus on what matters most:
- High Priority
- Medium Priority
- Low Priority

### Subtasks
Break down complex tasks into smaller, manageable subtasks.

## Notes
Use the Notes tab in the widget to capture quick thoughts, ideas, and important information.
MARKDOWN;
    }

    private function getVoiceContent(): string
    {
        return <<<'MARKDOWN'
# Voice Intelligence

Transform your voice into intelligent, structured content.

## How to Use Voice Intelligence

1. Click the **microphone icon** in the task widget
2. Click **Start Recording**
3. Speak naturally about your task or idea
4. Click **Stop Recording** when finished
5. Our AI will:
   - Transcribe your speech
   - Extract key points
   - Suggest task breakdowns
   - Create structured notes

## Best Practices

- Speak clearly and at a normal pace
- Mention specific details (dates, names, requirements)
- Describe the overall goal first, then details
- Review and refine the AI-generated output

## Supported Languages
Currently supports English with more languages coming soon.
MARKDOWN;
    }

    private function getBreakdownContent(): string
    {
        return <<<'MARKDOWN'
# Task Breakdown

Let AI help you break down complex tasks into manageable steps.

## How It Works

1. Create or select a task
2. Click the **Breakdown** button
3. Our AI analyzes your task and:
   - Identifies subtasks
   - Suggests time estimates
   - Recommends priorities
   - Provides implementation steps

## Example

**Original Task**: "Launch new marketing campaign"

**AI Breakdown**:
1. Define target audience and messaging (2 hours)
2. Create content assets (4 hours)
3. Set up social media scheduler (1 hour)
4. Design email templates (3 hours)
5. Configure analytics tracking (1 hour)
6. Launch and monitor (ongoing)

Review and customize the breakdown to fit your needs.
MARKDOWN;
    }

    private function getResearchContent(): string
    {
        return <<<'MARKDOWN'
# Research Engine

Conduct deep research and competitive intelligence gathering with AI assistance.

## Starting a Research Project

1. Navigate to **Research Engine** in the sidebar
2. Click **Start New Research**
3. Enter your research topic or question
4. Select research depth:
   - Quick Overview
   - Standard Research
   - Deep Dive

## What the AI Researches

- Competitor analysis
- Market trends
- Industry insights
- Technical documentation
- Best practices
- Case studies

## Research Output

Your research will include:
- Executive summary
- Detailed findings
- Sources and citations
- Actionable insights
- Related topics for further exploration

## Saving Research

All research is automatically saved to your **Documents** for future reference.
MARKDOWN;
    }

    private function getContentCreatorContent(): string
    {
        return <<<'MARKDOWN'
# Content Creator

Generate high-quality content for blogs, social media, and marketing materials.

## Generating Content

1. Go to **Content Creator** in the sidebar
2. Select content type:
   - Blog Post
   - Social Media Post
   - Email Copy
   - Product Description
3. Enter your topic or brief
4. Choose tone and style
5. Click **Generate**

## Content Options

### Blog Posts
- SEO-optimized
- Structured with headings
- Includes meta description
- Customizable length

### Social Posts
- Platform-specific formatting
- Hashtag suggestions
- Optimal posting times
- Multiple variations

## Editing & Refinement

- Edit content inline
- Request AI refinements
- Save drafts
- Publish directly to social planner

## Brand Voice

Connect to your **Brand Kits** to maintain consistent voice and style across all content.
MARKDOWN;
    }

    private function getMediaGenerationContent(): string
    {
        return <<<'MARKDOWN'
# AI Media Generation

Create stunning visuals to accompany your content.

## Generating Images

1. In the Content Creator, click **Generate Media**
2. Describe the image you want
3. Select style:
   - Photorealistic
   - Illustration
   - Abstract
   - Brand-specific
4. Choose dimensions
5. Click **Generate**

## Image Options

- Multiple variations per prompt
- HD and 4K quality
- Commercial license included
- Automatic optimization for web

## Best Practices

### Effective Prompts
- Be specific about subject matter
- Mention desired mood/atmosphere
- Specify colors or style references
- Include composition details

### Example Prompts
- "Modern office workspace, natural lighting, minimalist design"
- "Professional headshot, business casual, studio background"
- "Abstract tech background, blue and purple gradient"

All generated media is automatically saved to your **Media Registry**.
MARKDOWN;
    }

    private function getSocialPlannerContent(): string
    {
        return <<<'MARKDOWN'
# Social Planner

Schedule and manage your social media presence across all platforms.

## Scheduling Posts

1. Navigate to **Social Planner**
2. Click **New Post**
3. Select platforms (Facebook, Twitter, LinkedIn, Instagram)
4. Write your content
5. Upload media (optional)
6. Choose date and time
7. Click **Schedule**

## Calendar View

View all scheduled posts in a visual calendar:
- Drag and drop to reschedule
- Color-coded by platform
- Quick edit and preview

## Platform Integration

### Connecting Accounts
1. Go to **Settings** → **Integrations**
2. Click **Connect** for each platform
3. Authorize ArchitGrid
4. Start posting!

### Supported Platforms
- Facebook (Pages and Groups)
- Twitter/X
- LinkedIn (Personal and Company)
- Instagram (Business accounts)

## Post Analytics

Track performance:
- Engagement rates
- Reach and impressions
- Click-through rates
- Best posting times

## Content Suggestions

Our AI suggests:
- Optimal posting times
- Trending hashtags
- Content ideas based on your niche
- Engagement-boosting strategies
MARKDOWN;
    }

    private function getKnowledgeBaseContent(): string
    {
        return <<<'MARKDOWN'
# Knowledge Base

Build your intelligent knowledge repository for AI-powered insights.

## Adding Knowledge Assets

1. Go to **Knowledge Base**
2. Click **Add Asset**
3. Choose asset type:
   - Documents (PDF, DOCX, TXT)
   - Web Pages (URL)
   - Text Notes
   - API Integrations
4. Upload or enter content
5. Add tags and categories

## How It Works

Your knowledge base:
- Powers AI agents with domain-specific knowledge
- Improves research quality
- Enhances content generation
- Provides context for intelligent responses

## Organization

### Categories
Create custom categories:
- Company Policies
- Product Information
- Industry Research
- Competitor Intelligence
- Customer Data

### Tags
Add tags for quick filtering and discovery.

### Search
Powerful semantic search finds relevant information even without exact keyword matches.

## Privacy & Security

- Knowledge assets are tenant-isolated
- Access controls based on user roles
- Encrypted at rest and in transit
MARKDOWN;
    }

    private function getAiAgentsContent(): string
    {
        return <<<'MARKDOWN'
# AI Agents

Create custom AI assistants tailored to your specific needs.

## Creating an Agent

1. Navigate to **AI Agents**
2. Click **Create New Agent**
3. Enter agent details:
   - Name
   - Description
   - Role/Purpose
4. Configure knowledge sources
5. Set behavior and tone
6. Click **Create**

## Agent Configuration

### Knowledge Sources
Connect your agent to:
- Knowledge Base assets
- Specific documents
- Web pages
- Custom training data

### Behavior Settings
- Response style (formal, casual, technical)
- Length preferences (concise, detailed)
- Expertise level
- Language and tone

### Capabilities
Enable specific capabilities:
- Research and analysis
- Content generation
- Data processing
- Code assistance
- Customer support

## Using Agents

### Chat Interface
- Ask questions in natural language
- Get contextual responses
- Request specific actions
- Iterate and refine

### Integration
- Embed in your workflows
- Connect to external tools
- Automate repetitive tasks
- Schedule regular reports

## Best Practices

- Provide clear, specific instructions
- Feed relevant knowledge assets
- Test with diverse queries
- Refine based on performance
- Update knowledge regularly
MARKDOWN;
    }

    private function getDocumentBuilderContent(): string
    {
        return <<<'MARKDOWN'
# Document Builder

Generate professional documents with AI assistance.

## Supported Documents

- Resumes & CVs
- Cover Letters
- Business Proposals
- Reports
- Presentations
- Contracts
- Policies

## Creating a Document

1. Go to **Document Builder**
2. Select document type
3. Choose template (or start blank)
4. Fill in required information
5. Let AI help with content
6. Preview and customize
7. Export in your preferred format

## AI Features

### Content Generation
- Professional summaries
- Achievement descriptions
- Technical specifications
- Executive summaries

### Formatting
- Automatic layout optimization
- Consistent styling
- Professional typography
- Brand-aligned design

### Export Options
- PDF (high-quality)
- DOCX (editable)
- HTML
- Markdown

## Templates

Choose from professionally designed templates:
- Modern
- Classic
- Creative
- Executive
- Technical

Or create custom templates for your organization.

## Resume Builder

Special features for resume building:
- Parse existing resumes
- Optimize for ATS (Applicant Tracking Systems)
- Skill highlighting
- Achievement quantification
- Industry-specific formatting
MARKDOWN;
    }

    private function getMediaRegistryContent(): string
    {
        return <<<'MARKDOWN'
# Media Registry

Centralized hub for all your media assets.

## Adding Media

1. Navigate to **Media Registry**
2. Click **Upload**
3. Select files or drag & drop
4. Add metadata:
   - Title
   - Description
   - Tags
   - Category
5. Click **Save**

## Supported Formats

### Images
- JPG, PNG, GIF, WebP
- SVG (vector graphics)
- RAW formats

### Videos
- MP4, WebM, MOV
- Automatic transcoding
- Thumbnail generation

### Documents
- PDF, DOCX, XLSX
- Preview generation
- Text extraction

## Organization

### Folders
Create custom folder structures for organization.

### Tags
Add multiple tags for easy discovery.

### Collections
Group related assets into collections.

## Features

### Smart Search
- Search by filename
- Find by content (AI-powered)
- Filter by type, date, size
- Semantic search

### Optimization
- Automatic image compression
- Format conversion
- Responsive variants
- CDN delivery

### Usage Tracking
- See where assets are used
- Track downloads
- Monitor performance

## Integration

Your media registry integrates with:
- Content Creator
- Social Planner
- Document Builder
- Brand Kits
MARKDOWN;
    }

    private function getBrandKitsContent(): string
    {
        return <<<'MARKDOWN'
# Brand Kits

Manage your brand identity and maintain consistency across all content.

## Creating a Brand Kit

1. Go to **Settings** → **Brand Kits**
2. Click **Create New Brand**
3. Enter brand information:
   - Brand name
   - Tagline
   - Description
   - Industry
4. Add visual assets:
   - Logo variations
   - Color palette
   - Typography
   - Brand imagery
5. Define brand voice:
   - Tone (professional, casual, friendly)
   - Key messages
   - Do's and don'ts
   - Sample content

## Brand Elements

### Visual Identity
- Primary and secondary logos
- Color schemes (HEX, RGB, CMYK)
- Typography specifications
- Icon sets
- Image style guidelines

### Voice & Messaging
- Mission and vision
- Value propositions
- Key messaging points
- Writing style guide
- Brand personality traits

### Usage Guidelines
- Logo placement rules
- Color combinations
- Typography hierarchy
- Image treatment
- Social media templates

## Using Brand Kits

### Content Generation
AI automatically applies your brand voice when creating:
- Blog posts
- Social media content
- Marketing copy
- Email campaigns

### Design Assets
Access brand-approved assets in:
- Content Creator
- Social Planner
- Document Builder

### Multiple Brands
Create separate brand kits for:
- Different product lines
- Sub-brands
- Client brands (agencies)
- Regional variations

## Brand Scraper

Automatically extract brand elements from any website:
1. Enter website URL
2. AI extracts:
   - Logos
   - Colors
   - Fonts
   - Messaging
3. Review and refine
4. Save to brand kit
MARKDOWN;
    }

    private function getTeamManagementContent(): string
    {
        return <<<'MARKDOWN'
# Team Management

Invite and manage team members with role-based access controls.

## Inviting Team Members

1. Go to **Settings** → **Team Management**
2. Click **Invite User**
3. Enter email address
4. Select role:
   - Admin (full access)
   - Editor (create/edit content)
   - Viewer (read-only)
   - Custom role
5. Click **Send Invitation**

## User Roles

### Admin
- Full platform access
- User management
- Billing and settings
- Access to all features

### Editor
- Create and edit content
- Manage own tasks
- Access knowledge base
- Use AI features

### Viewer
- View content
- Read-only access
- Cannot create or edit
- Limited downloads

### Custom Roles
Create custom roles with specific permissions for:
- Feature access
- Content types
- Data visibility
- Export capabilities

## Managing Users

### Active Users
- View all team members
- Edit roles and permissions
- Deactivate accounts
- Reset passwords

### Invitation Management
- Resend invitations
- Cancel pending invites
- Set expiration dates

## Team Activity

Monitor team activity:
- Login history
- Content creation
- Feature usage
- Collaboration metrics

## Multi-Tenant Support (Agencies)

For agency accounts:
- Create sub-accounts for clients
- Manage multiple tenants
- Isolated data per client
- White-label options
MARKDOWN;
    }

    private function getAccessPoliciesContent(): string
    {
        return <<<'MARKDOWN'
# Access Policies

Define granular access controls for your team (Agency plans only).

## What Are Access Policies?

Access policies allow you to:
- Control feature access by role
- Restrict sensitive data
- Define content permissions
- Set usage limits
- Enforce security rules

## Creating a Policy

1. Go to **Settings** → **Access Policies**
2. Click **Create Policy**
3. Enter policy details:
   - Name
   - Description
   - Scope
4. Define rules:
   - Who (users/roles)
   - What (features/data)
   - When (time-based)
   - How (actions allowed)
5. Click **Save**

## Policy Types

### Feature Access
Control access to:
- Research Engine
- Content Creator
- AI Agents
- Document Builder
- Analytics

### Data Access
Restrict access to:
- Specific projects
- Client data
- Sensitive documents
- Financial information

### Action Permissions
Define allowed actions:
- Create/Read/Update/Delete
- Export/Download
- Share/Publish
- API access

### Time-Based Policies
Set policies that:
- Expire after a date
- Are active only during business hours
- Require periodic re-authentication

## Policy Examples

### "Content Team" Policy
- Access: Content Creator, Social Planner, Media Registry
- Cannot: Access billing, invite users, delete brand kits
- Limit: 100 AI generations per day

### "Client Viewer" Policy
- Access: View assigned projects only
- Cannot: Edit, delete, or export
- Limit: Read-only access

### "Manager" Policy
- Access: All features except billing
- Can: Manage team, create policies
- Limit: Cannot delete agency settings

## Enforcement

Policies are enforced:
- At login
- On feature access
- During API calls
- Real-time updates

## Audit Logging

All policy events are logged:
- Policy creation/modification
- Access granted/denied
- Policy violations
- Admin actions
MARKDOWN;
    }

    private function getAnalyticsContent(): string
    {
        return <<<'MARKDOWN'
# Analytics

Track performance, insights, and platform usage.

## Dashboard Overview

Your analytics dashboard shows:
- Content performance
- Team activity
- AI usage statistics
- Feature adoption
- Cost tracking

## Content Analytics

### Performance Metrics
- Views and engagement
- Social media reach
- Click-through rates
- Conversion tracking
- SEO rankings

### Content Breakdown
- Top-performing pieces
- Content by type
- Publishing frequency
- Platform distribution

## Team Analytics

### User Activity
- Active users
- Feature usage by user
- Content creation stats
- Collaboration metrics

### Productivity
- Tasks completed
- Time saved with AI
- Research projects
- Documents generated

## AI Usage

Track your AI consumption:
- Generation requests
- Token usage
- Cost per feature
- Optimization suggestions

## Custom Reports

Create custom reports:
1. Select metrics
2. Choose date range
3. Apply filters
4. Export as PDF or CSV

## Scheduled Reports

Set up automated reports:
- Daily summaries
- Weekly overviews
- Monthly analytics
- Quarterly reviews

Delivered via:
- Email
- Slack
- Dashboard
- API

## Insights & Recommendations

AI-powered insights:
- Optimize posting times
- Identify trending topics
- Suggest content gaps
- Predict performance
- Resource recommendations
MARKDOWN;
    }
}
