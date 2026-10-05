<x-filament-panels::page>
    <div class="space-y-6">

        @if ($recoveryCodesToShow)
            <div class="rounded-xl border border-yellow-300 bg-yellow-50 p-6 dark:border-yellow-800 dark:bg-yellow-950">
                <h3 class="text-base font-semibold text-yellow-800 dark:text-yellow-200">
                    Simpan kode pemulihan Anda
                </h3>
                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                    Kode ini hanya ditampilkan sekali. Simpan di tempat yang aman — setiap kode hanya
                    bisa dipakai satu kali untuk masuk jika Anda kehilangan akses ke aplikasi autentikator.
                </p>
                <div class="mt-4 grid grid-cols-2 gap-2 font-mono text-sm sm:grid-cols-4">
                    @foreach ($recoveryCodesToShow as $recoveryCode)
                        <div class="rounded-lg bg-white px-3 py-2 text-center dark:bg-gray-900">{{ $recoveryCode }}</div>
                    @endforeach
                </div>
                <button
                    type="button"
                    wire:click="$set('recoveryCodesToShow', null)"
                    class="mt-4 rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700"
                >
                    Sudah saya simpan
                </button>
            </div>
        @endif

        @if ($pendingSecret)
            {{-- Step 2: secret generated, waiting for the admin to confirm a code from their app --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Selesaikan pengaturan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Tambahkan kunci berikut ke aplikasi autentikator Anda (Google Authenticator, Authy, 1Password, dll)
                    menggunakan opsi &ldquo;masukkan kunci secara manual&rdquo;, lalu masukkan kode 6 digit yang muncul.
                </p>

                <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-900">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Kunci manual</p>
                    <p class="mt-1 break-all font-mono text-sm text-gray-950 dark:text-white">{{ $pendingSecret }}</p>
                </div>

                <div class="mt-4">
                    <label for="confirmCode" class="text-sm font-medium text-gray-950 dark:text-white">Kode dari aplikasi autentikator</label>
                    <input
                        type="text"
                        id="confirmCode"
                        wire:model="confirmCode"
                        inputmode="numeric"
                        maxlength="6"
                        placeholder="000000"
                        class="mt-1 block w-40 rounded-lg border-gray-300 text-center font-mono text-lg tracking-widest focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                    >
                </div>

                <div class="mt-4 flex gap-3">
                    <button
                        type="button"
                        wire:click="confirmTwoFactor"
                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700"
                    >
                        Konfirmasi &amp; Aktifkan
                    </button>
                    <button
                        type="button"
                        wire:click="cancelSetup"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Batal
                    </button>
                </div>
            </div>
        @elseif (auth()->user()->hasTwoFactorEnabled())
            {{-- Step 3: 2FA is active --}}
            <div class="rounded-xl border border-green-300 bg-green-50 p-6 dark:border-green-800 dark:bg-green-950">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-green-600 dark:text-green-400">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                    <h3 class="text-base font-semibold text-green-800 dark:text-green-200">Autentikasi dua faktor aktif</h3>
                </div>
                <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                    Akun ini memerlukan kode dari aplikasi autentikator (atau kode pemulihan) setiap kali masuk.
                </p>
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                    Sisa kode pemulihan: <strong>{{ count(auth()->user()->two_factor_recovery_codes ?? []) }}</strong>
                </p>
                <button
                    type="button"
                    wire:click="regenerateRecoveryCodes"
                    wire:confirm="Kode pemulihan lama akan berhenti berfungsi. Lanjutkan?"
                    class="mt-4 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-white dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                >
                    Buat ulang kode pemulihan
                </button>
            </div>

            <div class="rounded-xl border border-red-200 bg-white p-6 dark:border-red-900 dark:bg-gray-800">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Nonaktifkan 2FA</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Masukkan password Anda untuk menonaktifkan autentikasi dua faktor.
                </p>
                <div class="mt-4 flex flex-wrap items-end gap-3">
                    <div>
                        <label for="disablePassword" class="text-sm font-medium text-gray-950 dark:text-white">Password</label>
                        <input
                            type="password"
                            id="disablePassword"
                            wire:model="disablePassword"
                            class="mt-1 block w-64 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        >
                    </div>
                    <button
                        type="button"
                        wire:click="disableTwoFactor"
                        wire:confirm="Yakin ingin menonaktifkan autentikasi dua faktor?"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        Nonaktifkan
                    </button>
                </div>
            </div>
        @else
            {{-- Step 1: not enabled yet --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Autentikasi dua faktor belum aktif</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Tambahkan lapisan keamanan ekstra: selain email &amp; password, masuk juga akan meminta
                    kode 6 digit dari aplikasi autentikator di ponsel Anda. Disarankan untuk akun ini karena
                    panel admin mengelola pesanan dan bukti pembayaran pelanggan.
                </p>
                <button
                    type="button"
                    wire:click="generateSecret"
                    class="mt-4 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700"
                >
                    Aktifkan Autentikasi Dua Faktor
                </button>
            </div>
        @endif

    </div>
</x-filament-panels::page>
