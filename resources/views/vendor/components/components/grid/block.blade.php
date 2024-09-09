@props([
    'colSpan' => 1,
    'rowSpan' => 1,
    'gap' => 0,
    'rowStart' => 0,
    'rowEnd' => 0,
    'colStart' => 0,
    'colEnd' => 0
])

<div {{ $attributes->merge([
    'class' => 'col-span-' . $colSpan . ' row-span-' . $rowSpan . ' ' .
        (($gap > 0) ? 'gap-' . $gap : '') . ' ' .
        (($rowStart > 0) ? 'row-start-' . $rowStart : '') . ' ' .
        (($rowEnd > 0) ? 'row-end-' . $rowEnd : '') . ' ' .
        (($colStart > 0) ? 'col-start-' . $colStart : '') . ' ' .
        (($colEnd > 0) ? 'col-end-' . $colEnd : '')
]) }}>
    {{ $slot }}
</div>
