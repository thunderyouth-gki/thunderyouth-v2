<x-layouts.admin title="Admin Dashboard">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-text">Dashboard</h1>
        <p class="text-textlight mt-2">Welcome back, {{ auth()->user()->name }}. Here's an overview of the system.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Users Card -->
        <div class="bg-surface border border-accent rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-text">Users</h3>
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-text mb-2">{{ \App\Models\User::count() }}</div>
            <a href="{{ route('admin.users') }}" class="text-sm text-primary font-semibold hover:underline mt-4 inline-block">Manage Users &rarr;</a>
        </div>

        <!-- Roles Card -->
        <div class="bg-surface border border-accent rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-text">Roles</h3>
                <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center text-green-600 dark:text-green-500">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-text mb-2">{{ \Spatie\Permission\Models\Role::count() }}</div>
            <a href="{{ route('admin.roles') }}" class="text-sm text-primary font-semibold hover:underline mt-4 inline-block">Manage Roles &rarr;</a>
        </div>

        <!-- Permissions Card -->
        <div class="bg-surface border border-accent rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-text">Permissions</h3>
                <div class="w-10 h-10 rounded-full bg-orange-500/10 flex items-center justify-center text-orange-600 dark:text-orange-500">
                    <i class="fa-solid fa-key"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-text mb-2">{{ \Spatie\Permission\Models\Permission::count() }}</div>
            <a href="{{ route('admin.permissions') }}" class="text-sm text-primary font-semibold hover:underline mt-4 inline-block">Manage Permissions &rarr;</a>
        </div>
    </div>
</x-layouts.admin>
