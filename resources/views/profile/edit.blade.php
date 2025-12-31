@props([
    'user',
    'fields' => [],
    'focusedIndex' => 0,
    'errorMessage' => null,
    'successMessage' => null
])

<div class="w-full">
    {{-- Title --}}
    <div class="text-cyan font-bold">Edit your profile</div>
    <div class="text-cyan">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>
    <div class="mt-1"></div>

    {{-- Username Field --}}
    @php
        $usernameField = $fields['username'] ?? ['value' => $user->username, 'focused' => false, 'cursor' => 0];
        $usernameIsFocused = $focusedIndex === 0;
        $usernameLabelColor = $usernameIsFocused ? 'text-cyan' : 'text-gray';
        $usernameBorderColor = $usernameIsFocused ? 'text-cyan' : 'text-gray';
    @endphp
    
    <div class="{{ $usernameLabelColor }} font-bold">Username</div>
    <div class="{{ $usernameBorderColor }}">╭────────────────────────────────────────────────────────────╮</div>
    <div class="{{ $usernameBorderColor }}">│ <span class="text-white">{{ str_pad(mb_substr($usernameField['value'] ?? $user->username, 0, 58), 58) }}</span> │</div>
    <div class="{{ $usernameBorderColor }}">╰────────────────────────────────────────────────────────────╯</div>
    @if($usernameIsFocused)
        <div class="text-gray">{{ mb_strlen($usernameField['value'] ?? $user->username) }}/255 characters</div>
    @endif
    <div class="mt-1"></div>

    {{-- Bio Field --}}
    @php
        $bioField = $fields['bio'] ?? ['value' => $user->bio ?? '', 'focused' => false, 'cursor' => 0];
        $bioIsFocused = $focusedIndex === 1;
        $bioLabelColor = $bioIsFocused ? 'text-cyan' : 'text-gray';
        $bioBorderColor = $bioIsFocused ? 'text-cyan' : 'text-gray';
        $bioValue = $bioField['value'] ?? $user->bio ?? '';
        $bioLines = explode("\n", $bioValue);
        $visibleBioLines = array_slice($bioLines, 0, 4);
    @endphp
    
    <div class="{{ $bioLabelColor }} font-bold">Bio</div>
    <div class="{{ $bioBorderColor }}">╭────────────────────────────────────────────────────────────╮</div>
    @foreach($visibleBioLines as $line)
        <div class="{{ $bioBorderColor }}">│ <span class="text-white">{{ str_pad(mb_substr($line, 0, 58), 58) }}</span> │</div>
    @endforeach
    @for($i = count($visibleBioLines); $i < 4; $i++)
        <div class="{{ $bioBorderColor }}">│ {{ str_repeat(' ', 58) }} │</div>
    @endfor
    <div class="{{ $bioBorderColor }}">╰────────────────────────────────────────────────────────────╯</div>
    @if($bioIsFocused)
        <div class="text-gray">{{ mb_strlen($bioValue) }}/500 characters</div>
    @endif
    <div class="mt-1"></div>

    {{-- Messages --}}
    @if($errorMessage)
        <div class="text-red font-bold">✗ {{ $errorMessage }}</div>
        <div class="mt-1"></div>
    @endif

    @if($successMessage)
        <div class="text-green font-bold">✓ {{ $successMessage }}</div>
        <div class="mt-1"></div>
    @endif

    {{-- Instructions --}}
    <div class="text-cyan">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>
    <div class="flex justify-between text-gray">
        <span>Tab: next field • ↑↓: navigate • Enter: save • Esc: cancel</span>
        <span>powered by supo ◉</span>
    </div>
</div>
