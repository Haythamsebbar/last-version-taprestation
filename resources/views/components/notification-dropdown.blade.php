@php
    $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
    $recentNotifications = Auth::user()->notifications()
        ->whereNull('read_at')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
@endphp

<div class="relative" x-data="notificationDropdown()">
    <!-- Bouton de notification -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5a50.002 50.002 0 00-2.5-2.5V8a6 6 0 10-12 0v2.5c-1 1-2.5 2.5-2.5 2.5L5 17h5m5 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Badge de compteur -->
        @if($unreadCount > 0)
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" id="notification-badge">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    <!-- Dropdown des notifications -->
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">
                        Tout marquer comme lu
                    </button>
                </form>
                @endif
            </div>

            @if($recentNotifications->isEmpty())
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5a50.002 50.002 0 00-2.5-2.5V8a6 6 0 10-12 0v2.5c-1 1-2.5 2.5-2.5 2.5L5 17h5m5 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Aucune nouvelle notification</p>
                </div>
            @else
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($recentNotifications as $notification)
                        @php
                        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                        $title = $data['title'] ?? 'Notification';
                        $message = $data['message'] ?? '';
                        $url = $data['url'] ?? '#';
                        $type = $data['type'] ?? 'info';
                        @endphp
                        
                        <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" @click.prevent="showModal({{ $notification->id }}, @js($title), @js($message), @js($notification->created_at->diffForHumans()), @js($url))">
                            <!-- Icône selon le type -->
                            <div class="flex-shrink-0">
                                @if($type === 'new_offer')
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                @elseif($type === 'offer_accepted')
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @elseif($type === 'offer_rejected')
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Contenu de la notification -->
                            <div class="flex-1 min-w-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $title }}</p>
                                    <p class="text-sm text-gray-500 line-clamp-2">{{ $message }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <a href="{{ route('notifications.index') }}" class="block text-center text-sm text-indigo-600 hover:text-indigo-800">
                        Voir toutes les notifications
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de notification -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-all duration-300"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[80vh] overflow-y-auto p-8 flex flex-col items-center border border-gray-200 animate-fade-in">
            <button @click="closeModal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h2 class="text-2xl font-bold text-gray-900 mb-3 text-center w-full" x-text="modalTitle"></h2>
            <p class="text-gray-700 whitespace-pre-line mb-6 text-center w-full" x-text="modalMessage"></p>
            <p class="text-xs text-gray-400 mb-4 text-center w-full" x-text="modalTime"></p>
            <template x-if="modalUrl && modalUrl !== '#'">
                <a :href="modalUrl" class="text-indigo-600 hover:text-indigo-800 underline text-sm font-medium" target="_blank">Voir plus</a>
            </template>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        modalOpen: false,
        modalTitle: '',
        modalMessage: '',
        modalTime: '',
        modalUrl: '',
        showModal(id, title, message, time, url) {
            this.modalTitle = title;
            this.modalMessage = message;
            this.modalTime = time;
            this.modalUrl = url;
            this.modalOpen = true;
            // Mark as read via AJAX
            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
        },
        closeModal() {
            this.modalOpen = false;
        }
    }
}
</script>

<style>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px) scale(0.98); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-fade-in {
  animation: fade-in 0.3s cubic-bezier(0.4,0,0.2,1);
}
</style>