{{-- Document Builder - Recipient Information --}}
<div class="space-y-4 border-t pt-4">
    <label class="text-sm font-semibold text-primary">Recipient Information</label>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
            <label class="text-sm font-medium leading-none">Full Name</label>
            <input x-model="recipientName" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="e.g. John Doe" />
        </div>
        <div class="space-y-2">
            <label class="text-sm font-medium leading-none">Title / Role</label>
            <input x-model="recipientTitle" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="e.g. CEO" />
        </div>
    </div>
</div>
