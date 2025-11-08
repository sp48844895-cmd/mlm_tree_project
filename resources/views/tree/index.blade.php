<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MLM Tree</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link
        href="https://fonts.googleapis.com/css?family=Solway:400&display=swap"
        rel="stylesheet"
    >
    <style>
        body {
            font-family: "Solway", serif;
            font-size: 15px;
        }

        .body.genealogy-body {
            white-space: nowrap;
            overflow-y: hidden;
            padding: 10px 50px 50px;
            min-height: 500px;
            text-align: center;
        }

        .genealogy-scroll {
            overflow-x: auto;
            overflow-y: hidden;
        }

        .genealogy-scroll::-webkit-scrollbar {
            width: 5px;
            height: 8px;
        }

        .genealogy-scroll::-webkit-scrollbar-track {
            border-radius: 10px;
            background-color: #e4e4e4;
        }

        .genealogy-scroll::-webkit-scrollbar-thumb {
            background: #212121;
            border-radius: 10px;
            transition: 0.5s;
        }

        .genealogy-scroll::-webkit-scrollbar-thumb:hover {
            background: #d5b14c;
            transition: 0.5s;
        }

        .genealogy-body {
            white-space: nowrap;
            padding: 10px 50px 50px;
            min-height: 500px;
            text-align: center;
        }

        .genealogy-tree {
            display: inline-block;
        }

        .genealogy-tree ul {
            padding-top: 20px;
            position: relative;
            padding-left: 0;
            display: flex;
            justify-content: center;
            flex-wrap: nowrap;
            gap: 0;
            margin: 0;
        }

        .genealogy-tree ul ul {
            padding-top: 20px;
        }

        .genealogy-tree li {
            float: left;
            text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 5px 0 5px;
        }

        .genealogy-tree li::before,
        .genealogy-tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 2px solid #ccc;
            width: 50%;
            height: 18px;
        }

        .genealogy-tree li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid #ccc;
        }

        .genealogy-tree li:only-child::after,
        .genealogy-tree li:only-child::before {
            display: none;
        }

        .genealogy-tree li:only-child {
            padding-top: 0;
        }

        .genealogy-tree li:first-child::before,
        .genealogy-tree li:last-child::after {
            border: 0 none;
        }

        .genealogy-tree li:last-child::before {
            border-right: 2px solid #ccc;
            border-radius: 0 5px 0 0;
        }

        .genealogy-tree li:first-child::after {
            border-radius: 5px 0 0 0;
        }

        .genealogy-tree ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            width: 0;
            height: 20px;
        }

        .genealogy-tree li a {
            text-decoration: none;
            color: #333;
            display: inline-block;
        }

        .genealogy-tree li a:hover,
        .genealogy-tree li a:hover + ul li a {
            background: #c8e4f8;
            color: #000;
        }

        .genealogy-tree li a:hover + ul li::after,
        .genealogy-tree li a:hover + ul li::before,
        .genealogy-tree li a:hover + ul::before,
        .genealogy-tree li a:hover + ul ul::before {
            border-color: #fbba00;
        }

        .member-view-box {
            padding-bottom: 10px;
            text-align: center;
            border-radius: 4px;
            border: 1px solid #e4e4e4;
            background-color: #fff;
            transition: box-shadow 0.2s ease;
            display: inline-block;
        }

        .member-view-box:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }

        .member-view-box.is-current {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .member-view-box.is-current .member-header {
            background: #0d6efd;
        }

        .member-header {
            padding: 5px 0;
            text-align: center;
            background: #334455;
            color: #fff;
            font-size: 14px;
            border-radius: 4px 4px 0 0;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .member-image {
            padding: 12px;
            width: 120px;
            margin: 0 auto;
        }

        .member-image img {
            width: 96px;
            height: 96px;
            border-radius: 8px;
            object-fit: cover;
            background-color: #fff;
        }

        .member-footer {
            padding: 0 12px 4px;
            text-align: center;
        }

        .member-footer .name {
            color: #000;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .member-footer .email {
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .member-footer .downline {
            color: #000;
            font-size: 12px;
            font-weight: bold;
        }

        .referral-alert {
            background: linear-gradient(135deg, #fef7e6 0%, #ffe9d6 100%);
            border-color: #ffd9a0;
            color: #7c4a03;
        }

        .referral-alert .input-group input {
            background-color: rgba(255, 255, 255, 0.85);
        }

        .referral-alert .btn-outline-secondary {
            border-color: rgba(124, 74, 3, 0.4);
            color: #7c4a03;
        }

        .referral-alert .btn-outline-secondary:hover {
            background-color: rgba(124, 74, 3, 0.1);
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">MLM Tree Overview</h1>
            <p class="text-muted mb-0">Visualize how users are linked within your referral network.</p>

            @if ($isAuthenticated)
                <div class="alert alert-secondary mt-3 mb-0 py-2 px-3 small d-inline-flex align-items-center gap-2">
                    <span class="badge text-bg-primary">Logged in</span>
                    <span>{{ auth()->user()->name }}</span>
                    <span class="text-muted">({{ auth()->user()->email }})</span>
                </div>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('home') }}">Refresh</a>

            @if ($isAuthenticated)
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Log out</button>
                </form>
            @else
                <a class="btn btn-outline-primary" href="{{ route('login') }}">Log in</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Register</a>
            @endif
        </div>
    </div>

    @if (session('status') || session('referral_link') || $userReferralLink)
        <div class="alert alert-success referral-alert alert-dismissible fade show" role="alert">
            @if (session('status'))
                <div>{{ session('status') }}</div>
            @endif

            @php
                $referralLink = session('referral_link') ?? $userReferralLink;
            @endphp

            @if ($referralLink)
                <div class="mt-3">
                    <label class="form-label fw-semibold">Share your referral link</label>
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            value="{{ $referralLink }}"
                            id="referral-link-input"
                            readonly
                        >
                        <button class="btn btn-outline-secondary" type="button" id="copy-referral-link">
                            Copy
                        </button>
                    </div>
                    <div class="form-text">Send this link to invite others. Their referral code will be pre-filled.</div>
                </div>
            @endif

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($isAuthenticated)
        @if (empty($tree))
            <div class="alert alert-info">
                The MLM tree is currently empty. Start by inviting users with your referral link.
            </div>
        @else
            <div class="genealogy-body genealogy-scroll">
                <div class="genealogy-tree">
                    <ul>
                        @foreach ($tree as $node)
                            @include('tree.partials.node', ['node' => $node, 'isActive' => true])
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4">Build Your Network</h2>
                <p class="text-muted mb-4">
                    Join our referral program to unlock commissions and track the members in your team. Create
                    an account to receive your unique referral link and start inviting others.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Create Your Account</a>
                    <a class="btn btn-outline-secondary btn-lg" href="{{ route('login') }}">Log in</a>
                </div>
            </div>
        </div>
    @endif
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>
<script>
    (function () {
        const copyButton = document.getElementById('copy-referral-link');
        const input = document.getElementById('referral-link-input');

        if (!copyButton || !input) {
            return;
        }

        copyButton.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(input.value);
                copyButton.textContent = 'Copied!';
                setTimeout(() => {
                    copyButton.textContent = 'Copy';
                }, 2000);
            } catch (error) {
                copyButton.textContent = 'Failed';
                setTimeout(() => {
                    copyButton.textContent = 'Copy';
                }, 2000);
            }
        });
    })();

    (function () {
        const tree = document.querySelector('.genealogy-tree');
        if (!tree) {
            return;
        }

        const rootUl = tree.querySelector(':scope > ul');
        const allLists = tree.querySelectorAll('ul');

        const showList = (ul) => {
            ul.style.display = 'flex';
        };

        const hideList = (ul) => {
            ul.style.display = 'none';
        };

        allLists.forEach((ul) => {
            if (ul === rootUl || ul.classList.contains('active')) {
                showList(ul);
            } else {
                hideList(ul);
            }
        });

        tree.querySelectorAll('li > a').forEach((anchor) => {
            anchor.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                const parentLi = anchor.parentElement;
                const childList = parentLi.querySelector(':scope > ul');

                if (!childList) {
                    return;
                }

                const isVisible = childList.style.display !== 'none';
                if (isVisible) {
                    hideList(childList);
                    childList.classList.remove('active');
                } else {
                    showList(childList);
                    childList.classList.add('active');
                }
            });
        });
    })();
</script>
</body>
</html>

