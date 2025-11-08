@php
    $user = $node['user'] ?? null;
    $name = $user->name ?? 'Unknown User';
    $email = $user->email ?? 'N/A';
    $avatarUrl = $user?->avatar_url;
    $avatarUrl = $avatarUrl ?: 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0D8ABC&color=fff&size=128';
    $level = $node['level'] ?? 0;
    $children = $node['children'] ?? [];
    $direct = $node['direct_count'] ?? count($children);
    $descendants = $node['descendants'] ?? $direct;
    $isActive = $isActive ?? false;
    $isCurrentUser = auth()->check() && $user && $user->id === auth()->id();
    $headerLabel = $isCurrentUser ? 'You' : ($level === 0 ? 'Leader' : 'Level ' . $level);
    $cardClass = $isCurrentUser ? 'member-view-box is-current' : 'member-view-box';
@endphp
<li>
    <a href="javascript:void(0);" aria-label="Toggle {{ $name }}'s downline">
        <div class="{{ $cardClass }}">
            <div class="member-header">
                <span>{{ $headerLabel }}</span>
            </div>
            <div class="member-image">
                <img src="{{ $avatarUrl }}" alt="{{ $name }}">
            </div>
            <div class="member-footer">
                <div class="name"><span>{{ $name }}</span></div>
                <div class="email"><span>{{ $email }}</span></div>
                <div class="downline"><span>{{ $direct }} | {{ $descendants }}</span></div>
            </div>
        </div>
    </a>

    @if (!empty($children))
        <ul class="{{ $isActive ? 'active' : '' }}">
            @foreach ($children as $child)
                @include('tree.partials.node', ['node' => $child, 'isActive' => false])
            @endforeach
        </ul>
    @endif
</li>
