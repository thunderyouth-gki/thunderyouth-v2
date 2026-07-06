<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Permissions Management</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage individual permissions available in the system.</p>
        </div>
        <flux:button class="bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 text-white border-transparent transition-all shadow-soft cursor-pointer">New Permission</flux:button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">No</th>
                        <th scope="col" class="px-6 py-4 font-semibold w-full">Permission Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($permissions as $index => $permission)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $permission->name }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <flux:button size="sm" variant="filled" class="bg-amber-500 hover:bg-amber-600 text-white">Edit</flux:button>
                                    <flux:button size="sm" variant="danger">Delete</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
