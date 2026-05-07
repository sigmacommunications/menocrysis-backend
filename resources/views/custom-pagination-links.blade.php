<style>
.pagination {
  display: inline-block;
}

.pagination a {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  transition: background-color .3s;
  border: 1px solid #ddd;
}

.pagination a.active {
  background-color: #4CAF50;
  color: white;
  border: 1px solid #4CAF50;
}

.pagination a:hover:not(.active) {background-color: #ddd;}
</style>
<div class="pagination">
    @if ($paginator->onFirstPage())
        <a href="#"  class="disabled"><span>← Previous</span></a>
    @else
        <a href="{{ $paginator->withQueryString()->previousPageUrl() }}"  class="disabled"><span>← Previous</span></a>
    @endif
    @foreach ($elements as $element)
        @if (is_string($element))
            <a class="disabled">{{ $element }}</a>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <a href="#" class="active">{{ $page }}</a>
                @else
                <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->withQueryString()->nextPageUrl() }}">Next</a>
    @else
        <li class="disabled"><span>Next</span></li>
    @endif
</div>
