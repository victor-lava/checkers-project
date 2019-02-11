<a  href="{{ isset($href) ? $href : '#' }}"
    class="btn{{ isset($className) ? " btn-$className" : ' btn-primary'}} {{ isset($size) ? " btn-$size" : '' }}">
    {{ $slot }}
</a>
