@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-lg shadow-lg p-6 md:p-8">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <!-- QR Code Section -->
            <div class="flex flex-col items-center justify-center text-center p-6 bg-gray-50 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Votre QR Code de Profil</h2>
                <div id="qrcode" class="p-4 bg-white border rounded-lg shadow-md"></div>
                <button id="download-btn" class="mt-6 inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 w-full md:w-auto">
                    Télécharger le QR Code (PNG)
                </button>
            </div>

            <!-- Information Section -->
            <div class="flex flex-col justify-center">
                <p class="text-gray-600 text-center md:text-left mb-6">
                    Scannez ce QR code pour accéder à votre profil public. Partagez-le sur vos supports ou cartes de visite.
                </p>

                <div class="mb-4">
                    <label for="profile_url" class="block text-sm font-medium text-gray-700 mb-1">Lien du profil public</label>
                    <div class="relative">
                        <input type="text" id="profile_url" readonly value="{{ $url }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button onclick="copyToClipboard('{{ $url }}')" class="absolute inset-y-0 right-0 px-4 flex items-center bg-gray-200 hover:bg-gray-300 rounded-r-lg text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                </div>

                <button onclick="copyToClipboard('{{ $url }}')" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                    Copier le lien du profil
                </button>
                <div id="copy-feedback" class="text-center text-green-500 mt-2 h-4"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const url = "{{ $url }}";
    const qrcodeContainer = document.getElementById('qrcode');

    const qrcode = new QRCode(qrcodeContainer, {
        text: url,
        width: 300,
        height: 300,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    document.getElementById('download-btn').addEventListener('click', function() {
        const img = qrcodeContainer.getElementsByTagName('img')[0];
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        const a = document.createElement('a');
        a.href = canvas.toDataURL('image/png');
        a.download = 'qrcode-profil.png';
        a.click();
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            const feedback = document.getElementById('copy-feedback');
            feedback.textContent = 'Lien copié !';
            setTimeout(() => { feedback.textContent = '' }, 3000);
        }, function(err) {
            const feedback = document.getElementById('copy-feedback');
            feedback.textContent = 'Erreur de copie.';
            feedback.classList.remove('text-green-500');
            feedback.classList.add('text-red-500');
            setTimeout(() => {
                 feedback.textContent = '';
                 feedback.classList.remove('text-red-500');
                 feedback.classList.add('text-green-500');
            }, 3000);
        });
    }
</script>
@endpush
@endsection