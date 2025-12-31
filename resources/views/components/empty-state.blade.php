@props(['title' => 'No posts yet', 'message' => 'Start sharing your thoughts', 'action' => null])

@php
    // Calculate dynamic width based on content
    $titleLen = mb_strlen($title);
    $messageLen = mb_strlen($message);
    $contentWidth = max($titleLen, $messageLen) + 8; // padding on sides
    $boxWidth = max(35, min($contentWidth, 50)); // min 35, max 50
    $innerWidth = $boxWidth - 2; // account for border chars
    
    // Center the content
    $titlePadLeft = (int) floor(($innerWidth - $titleLen) / 2);
    $titlePadRight = $innerWidth - $titleLen - $titlePadLeft;
    $msgPadLeft = (int) floor(($innerWidth - $messageLen) / 2);
    $msgPadRight = $innerWidth - $messageLen - $msgPadLeft;
@endphp

<div class="flex justify-center mt-4">
    <div class="text-center">
        {{-- Top border with rounded corners --}}
        <div class="text-cyan">╭{{ str_repeat('─', $innerWidth) }}╮</div>
        
        {{-- Empty line --}}
        <div><span class="text-cyan">│</span>{{ str_repeat(' ', $innerWidth) }}<span class="text-cyan">│</span></div>
        
        {{-- Title --}}
        <div><span class="text-cyan">│</span>{{ str_repeat(' ', $titlePadLeft) }}<span class="text-cyan font-bold">{{ $title }}</span>{{ str_repeat(' ', $titlePadRight) }}<span class="text-cyan">│</span></div>
        
        {{-- Empty line --}}
        <div><span class="text-cyan">│</span>{{ str_repeat(' ', $innerWidth) }}<span class="text-cyan">│</span></div>
        
        {{-- Message --}}
        <div><span class="text-cyan">│</span>{{ str_repeat(' ', $msgPadLeft) }}<span class="text-white">{{ $message }}</span>{{ str_repeat(' ', $msgPadRight) }}<span class="text-cyan">│</span></div>
        
        {{-- Empty line --}}
        <div><span class="text-cyan">│</span>{{ str_repeat(' ', $innerWidth) }}<span class="text-cyan">│</span></div>
        
        {{-- Action hint if provided --}}
        @if($action)
            @php
                $actionLen = mb_strlen($action);
                $actionPadLeft = (int) floor(($innerWidth - $actionLen) / 2);
                $actionPadRight = $innerWidth - $actionLen - $actionPadLeft;
            @endphp
            <div><span class="text-cyan">│</span>{{ str_repeat(' ', $actionPadLeft) }}<span class="text-gray">{{ $action }}</span>{{ str_repeat(' ', $actionPadRight) }}<span class="text-cyan">│</span></div>
            <div><span class="text-cyan">│</span>{{ str_repeat(' ', $innerWidth) }}<span class="text-cyan">│</span></div>
        @endif
        
        {{-- Bottom border with rounded corners --}}
        <div class="text-cyan">╰{{ str_repeat('─', $innerWidth) }}╯</div>
    </div>
</div>