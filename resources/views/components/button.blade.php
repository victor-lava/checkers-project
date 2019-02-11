<a  href="{{ isset($href) ? $href : '#' }}"
    class="btn{{ isset($className) ? " btn-$className" : ''}} {{ isset($size) ? " btn-$size" : '' }}">
    {{ $slot }}
</a>
