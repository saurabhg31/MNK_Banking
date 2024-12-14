<h2>Set up Two-Factor Authentication</h2>
<p>Scan the QR code below with your Google Authenticator app:</p>

<div>{!! $qrCodeSvg !!}</div>

<p>Or enter this secret key manually: <strong>{{ $secret }}</strong></p>

<form action="{{ route('verify.2fa') }}" method="POST">
    @csrf
    <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
    <label for="token">Enter the 6-digit code to confirm setup:</label>
    <input type="text" name="token" id="token" required>
    <button type="submit">Verify and Complete Registration</button>
</form>