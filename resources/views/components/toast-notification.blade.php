<!-- Global Toast Notification -->
<div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2"></div>

<script>
    if (typeof window.showNotification === 'undefined') {
        window.showNotification = function(message, type = 'success') {
            const toast = document.createElement('div');
            const icon = type === 'success' ? '<i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>' : '<i class="fa-solid fa-circle-exclamation text-red-400 text-sm"></i>';
            const timerColor = type === 'success' ? 'bg-emerald-400' : 'bg-red-400';
            
            toast.className = 'bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-glow slide-in-right text-xs font-semibold border border-slate-700 relative overflow-hidden';
            toast.innerHTML = `
                <div class="flex items-center gap-2 relative z-10">
                    ${icon} <span>${message}</span>
                </div>
                <div class="absolute bottom-0 left-0 h-1 ${timerColor} timer-bar"></div>
            `;
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        window.addEventListener('notify', event => {
            showNotification(event.detail.message, event.detail.type || 'success');
        });
    }
</script>
