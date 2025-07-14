<!-- Enable OTP Modal -->
<div class="modal fade" id="enableOTP" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2">Reset Password</h4>
          <p>You can reset your password here.</p>
        </div>
        <p><strong>Important:</strong> To reset your password, first enter your current password and click the <strong>Request Password Change</strong> button. A link with the token will be sent to your email address. Once you receive the link, you can use it to reset your password.</p>
        <form id="enableOTPForm" class="row g-5" onsubmit="return false">
          <div class="col-12">
            <label class="form-label" for="currentPassword">Current Password</label>
            <div class="input-group input-group-merge">
              <input type="password" id="currentPassword" name="currentPassword" class="form-control" placeholder="Enter your current password" />
              <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
            </div>
          </div>
          <div class="col-12">
            <!-- Request Token Button with Spinner -->
            <button type="button" class="btn btn-primary me-3" id="requestTokenButton">
              <span class="spinner-grow me-1" role="status" aria-hidden="true" style="display: none;"></span>
              Request Token
            </button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="/lib/js/page-scripts/modal-reset-password.js"></script>
