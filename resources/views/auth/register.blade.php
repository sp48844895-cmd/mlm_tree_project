<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
</head>
<body class="bg-light">
<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm w-100" style="max-width: 480px;">
        <div class="card-body p-4">
            <h1 class="h3 mb-4 text-center">Create an Account</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form id="registration-form" method="POST" action="{{ route('register.store') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input
                        type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label for="referral_code" class="form-label">Referral Code (optional)</label>
                    <input
                        type="text"
                        class="form-control @error('referral_code') is-invalid @enderror"
                        id="referral_code"
                        name="referral_code"
                        value="{{ old('referral_code', $referralCode) }}"
                        placeholder="Enter referral code"
                    >
                    <div
                        id="referral-code-feedback"
                        class="form-text {{ $errors->has('referral_code') ? 'text-danger' : '' }}"
                        data-default-error="{{ $errors->first('referral_code') ?? 'Referral code is invalid.' }}"
                    >
                        {{ $errors->first('referral_code') }}
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>

            <p class="mt-4 mb-0 text-center">
                Already have an account?
                <a href="{{ route('login') }}">Log in here</a>
            </p>
        </div>
    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>
<script>
    (function () {
        const form = document.getElementById('registration-form');
        const referralInput = document.getElementById('referral_code');
        const validateUrl = '{{ route('referral.validate') }}';
        const registerButton = form.querySelector('button[type="submit"]');
        const feedback = document.getElementById('referral-code-feedback');
        const defaultError = feedback.dataset.defaultError || 'Referral code is invalid.';

        let lastValue = referralInput.value;
        let controller;

        function updateFeedback(message = '', type = '') {
            feedback.textContent = message;
            feedback.classList.remove('text-danger', 'text-success', 'text-muted');
            if (type) {
                feedback.classList.add(type);
            }
        }

        function setValidity(valid, message = '', hasCode = false) {
            if (!hasCode) {
                referralInput.classList.remove('is-invalid', 'is-valid');
                updateFeedback('');
                registerButton.disabled = false;
                return;
            }

            if (valid) {
                referralInput.classList.remove('is-invalid');
                referralInput.classList.add('is-valid');
                updateFeedback(message || 'Referral code found.', 'text-success');
                registerButton.disabled = false;
            } else {
                referralInput.classList.remove('is-valid');
                referralInput.classList.add('is-invalid');
                updateFeedback(message || defaultError, 'text-danger');
                registerButton.disabled = true;
            }
        }

        async function validateReferral(code) {
            if (!code) {
                setValidity(true, '', false);
                return;
            }

            try {
                if (controller) {
                    controller.abort();
                }
                controller = new AbortController();

                const response = await fetch(`${validateUrl}?code=${encodeURIComponent(code)}`, {
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to validate referral code.');
                }

                const data = await response.json();
                setValidity(data.valid, data.message, true);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    setValidity(false, error.message, true);
                }
            }
        }

        referralInput.addEventListener('input', (event) => {
            const value = event.target.value.trim();
            lastValue = value;

            if (controller) {
                controller.abort();
            }

            if (!value) {
                setValidity(true, '', false);
                return;
            }

            registerButton.disabled = true;
            updateFeedback('Validating referral code...', 'text-muted');
            validateReferral(value);
        });

        if (lastValue) {
            validateReferral(lastValue);
        } else if (feedback.textContent.trim() === '') {
            updateFeedback('');
        }
    })();
</script>
</body>
</html>

