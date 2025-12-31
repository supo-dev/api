@props(['title' => 'No posts yet', 'message' => 'Start sharing your thoughts', 'action' => null])

@php
    $width = 40;
    $titleLen = mb_strlen($title);
    $msgLen = mb_strlen($message);
    $titlePadLeft = (int) floor(($width - $titleLen) / 2);
    $titlePadRight = $width - $titleLen - $titlePadLeft;
    $msgPadLeft = (int) floor(($width - $msgLen) / 2);
    $msgPadRight = $width - $msgLen - $msgPadLeft;
    $emptyLine = str_repeat("\u{00A0}", $width);
@endphp

<div class="flex justify-center mt-4">
    <div>
        <div class="text-cyan">╭{{ str_repeat('─', $width) }}╮</div>
        <div class="text-cyan">│{{ $emptyLine }}│</div>
        <div class="text-cyan">│{{ str_repeat("\u{00A0}", $titlePadLeft) }}<span class="font-bold">{{ $title }}</span>{{ str_repeat("\u{00A0}", $titlePadRight) }}│</div>
        <div class="text-cyan">│{{ $emptyLine }}│</div>
        <div class="text-cyan">│{{ str_repeat("\u{00A0}", $msgPadLeft) }}<span class="text-white">{{ $message }}</span>{{ str_repeat("\u{00A0}", $msgPadRight) }}│</div>
        <div class="text-cyan">│{{ $emptyLine }}│</div>
        @if($action)
            @php
                $actionLen = mb_strlen($action);
                $actionPadLeft = (int) floor(($width - $actionLen) / 2);
                $actionPadRight = $width - $actionLen - $actionPadLeft;
            @endphp
            <div class="text-cyan">│{{ str_repeat("\u{00A0}", $actionPadLeft) }}<span class="text-gray">{{ $action }}</span>{{ str_repeat("\u{00A0}", $actionPadRight) }}│</div>
            <div class="text-cyan">│{{ $emptyLine }}│</div>
        @endif
        <div class="text-cyan">╰{{ str_repeat('─', $width) }}╯</div>
    </div>
</div>