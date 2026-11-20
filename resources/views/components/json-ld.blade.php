@props(['data'])

{{-- A schema.org JSON-LD block. json_encode builds the JSON, so quotes and line breaks in the content stay valid;
     JSON_HEX_TAG escapes < and >, so a "</script>" in a title cannot close the tag. --}}
@if (!empty($data))
    <script type="application/ld+json">
        {!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endif
