@props(['title' => 'No posts yet', 'message' => 'Start sharing your thoughts', 'action' => null])

@php
    $width = 40;
    $pad = function($text, $len) use ($width) {
        $padding = $width - $len;
        $left = (int) floor($padding / 2);
        $right = $padding - $left;
        return str_repeat("\u{00A0}", $left) . $text . str_repeat("\u{00A0}", $right);
    };
    
    $titleLine = $pad($title, mb_strlen($title));
    $msgLine = $pad($message, mb_strlen($message));
    $emptyLine = str_repeat("\u{00A0}", $width);
@endphp

<div class="flex justify-center mt-4">
    <div>
        <div class="text-cyan">╭{{ str_repeat('─', $width) }}╮</div>
        <div class="text-cyan">│<span class="text-cyan">{{ $emptyLine }}</span>│</div>
        <div class="text-cyan">│<span class="text-cyan font-bold">{{ $titleLine }}</span>│</div>
        <div class="text-cyan">│<span class="text-cyan">{{ $emptyLine }}</span>│</div>
        <div class="text-cyan">│<span class="text-white">{{ $msgLine }}</span>│</div>
        <div class="text-cyan">│<span class="text-cyan">{{ $emptyLine }}</span>│</div>
        @if($action)
            @php
                $actionLine = $pad($action, mb_strlen($action));
            @endphp
            <div class="text-cyan">│<span class="text-gray">{{ $actionLine }}</span>│</div>
            <div class="text-cyan">│<span class="text-cyan">{{ $emptyLine }}</span>│</div>
        @endif
        <div class="text-cyan">╰{{ str_repeat('─', $width) }}╯</div>
    </div>
</div>