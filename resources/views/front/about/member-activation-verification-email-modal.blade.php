<!-- Modal -->
<div class="modal fade" id="member-activation-verification-email-modal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <form id="member-activation-verification-email-form"
                    action="{{ route('about.member-activation.verify-email') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="otp" class="form-label">Kode OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required maxlength="6"
                            autocomplete="off">
                        <div class="invalid-feedback">
                            @error('otp')
                                {{ $message }}
                            @enderror
                        </div>
                        <div class="form-text">
                            Silahkan masukkan kode OTP yang telah dikirim ke email
                            <span class="fw-bold" id="email-for-verification"></span>.
                        </div>
                        <button type="button" disabled class="btn btn-link btn-sm text-decoration-none p-0 text-brand"
                            id="resend-otp">
                            Kirim ulang OTP
                            <span class="time-remaining"></span>
                        </button>
                    </div>
                    <button id="btn-verify" type="submit" class="btn btn-custom">Verifikasi</button>
                </form>
            </div>
        </div>
    </div>
</div>
