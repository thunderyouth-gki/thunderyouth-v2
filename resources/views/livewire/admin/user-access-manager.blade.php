<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">User Access Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage roles and permissions assigned to specific users.</p>
        </div>
        <flux:button class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent transition-all shadow-soft cursor-pointer">New User</flux:button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">No</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Email</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Roles</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic text-xs">No role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button size="sm" variant="filled" class="bg-amber-500 hover:bg-amber-600 text-white">Manage Access</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
