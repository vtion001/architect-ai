{{--
    AI Agents Index Page
    
    Displays AI agent cards with CRUD operations.
    Modularized - uses @include for partials.
    
    Required variables:
    - $agents: Collection of AI agents
    - $knowledgeAssets: Collection of knowledge base assets
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showCreateModal: false,
    showEditModal: false,
    selectedAgent: null,
    newAgent: {
        name: '',
        role: '',
        goal: '',
        backstory: '',
        knowledge_sources: [],
        primary_color: '#00F2FF',
        welcome_message: 'Hello! How can I assist you today?',
        temperature: 0.7,
        widget_position: 'bottom-right'
    },
    isSaving: false,
    
    resetNewAgent() {
        this.newAgent = {
            name: '',
            role: '',
            goal: '',
            backstory: '',
            knowledge_sources: [],
            primary_color: '#00F2FF',
            welcome_message: 'Hello! How can I assist you today?',
            temperature: 0.7,
            widget_position: 'bottom-right'
        };
    },
    
    saveAgent() {
        if (!this.newAgent.name || !this.newAgent.role || !this.newAgent.goal) {
            alert('Name, Role, and Goal are mandatory.');
            return;
        }
        this.isSaving = true;
        fetch('{{ route('ai-agents.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(this.newAgent)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to create agent.');
                this.isSaving = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSaving = false;
        });
    },
    
    editAgent(agent) {
        this.selectedAgent = { ...agent };
        this.showEditModal = true;
    },
    
    updateAgent() {
        this.isSaving = true;
        fetch(`/ai-agents/${this.selectedAgent.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(this.selectedAgent)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to update agent.');
                this.isSaving = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSaving = false;
        });
    },
    
    deleteAgent(id) {
        if(confirm('Decommission this AI Agent?')) {
            fetch(`/ai-agents/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => window.location.reload());
        }
    }
}">
    {{-- Page Header --}}
    @include('ai-agents.partials.header')

    {{-- Agents Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($agents as $agent)
            @include('ai-agents.partials.agent-card', ['agent' => $agent, 'knowledgeAssets' => $knowledgeAssets])
        @empty
            @include('ai-agents.partials.empty-state')
        @endforelse
    </div>

    {{-- Create Agent Modal --}}
    @include('ai-agents.partials.modals.create-modal', ['knowledgeAssets' => $knowledgeAssets])

    {{-- Edit Agent Modal --}}
    @include('ai-agents.partials.modals.edit-modal')
</div>
@endsection
