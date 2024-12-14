<h2>Two-Factor Authentication</h2>
<p>Enter the 6-digit code from your Google Authenticator app:</p>
@if (isset($errors))
    <span style="color: red;">{{$errors->first()}}</span>
@endif
<form action="{{ route('verify.2fa.login') }}" method="POST">
    @csrf
    <label for="token">Authentication Code:</label>
    <input type="text" name="token" id="token" required>
    <button type="submit">Verify</button>
</form>