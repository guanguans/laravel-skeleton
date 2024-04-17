@push('modals-container')
    <div class="modal fade center-scale"
         id="screen-modal-{{$key}}"
         role="dialog"
         aria-labelledby="screen-modal-{{$key}}"
         data-controller="modal"
         data-modal-slug="{{$templateSlug}}"
         data-modal-async-enable="{{$asyncEnable}}"
         data-modal-async-route="{{$asyncRoute}}"
         data-modal-open="{{$open}}"
        {{$staticBackdrop ? "data-bs-backdrop=static" : ''}}
    >
        <div class="modal-dialog modal-fullscreen-md-down {{$size}} {{$type}}"
             role="document"
             id="screen-modal-type-{{$key}}"
        >
            <form class="modal-content"
                  action="{{ $method }}"
                  id="screen-modal-form-{{$key}}"
                  method="post"
                  enctype="multipart/form-data"
                  data-controller="form"
                  data-action="form#submit"
            >
                <div class="p-4 modal-placeholder">
                    <div class="placeholder-glow mb-3 mt-1 d-flex align-items-center">
                        <span class="placeholder col-6 h5 me-auto rounded-1"></span>
                        <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <p class="placeholder-glow">
                        <span class="placeholder col-7 rounded-1"></span>
                        <span class="placeholder col-4 rounded-1"></span>
                        <span class="placeholder col-4 rounded-1"></span>
                        <span class="placeholder col-6 rounded-1"></span>
                        <span class="placeholder col-8 rounded-1"></span>
                        <span class="placeholder col-7 rounded-1"></span>
                        <span class="placeholder col-4 rounded-1"></span>
                        <span class="placeholder col-4 rounded-1"></span>
                        <span class="placeholder col-6 rounded-1"></span>
                        <span class="placeholder col-8 rounded-1"></span>
                        <span class="placeholder col-3 rounded-1"></span>
                        <span class="placeholder col-7 rounded-1"></span>
                    </p>

                    <p class="placeholder-glow mb-0">
                        <span class="placeholder col-3 rounded-1"></span>
                        <span class="placeholder col-7 rounded-1"></span>
                        <span class="placeholder col-7 rounded-1"></span>
                        <span class="placeholder col-4 rounded-1"></span>
                        <span class="placeholder col-4 rounded-1"></span>
                        <span class="placeholder col-6 rounded-1"></span>
                        <span class="placeholder col-8 rounded-1"></span>
                        <span class="placeholder col-4 rounded-1"></span>
                    </p>
                </div>
                <div class="modal-header">
                    <h4 class="modal-title text-black fw-light" data-modal-target="title">{{$title}}</h4>
                    <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>
                </div>
                <div class="modal-body layout-wrapper">
                    <x-orchid-stream target="{{$templateSlug}}">
                        <div id="{{ $templateSlug }}">
                            @foreach($manyForms as $formKey => $modal)
                                @foreach($modal as $item)
                                    {!! $item ?? '' !!}
                                @endforeach
                            @endforeach
                            @csrf
                        </div>
                    </x-orchid-stream>
                </div>
                <div class="modal-footer">
                    @if(!$withoutCloseButton)
                        <button type="button" class="btn btn-link" data-bs-dismiss="modal">
                            {{ $close }}
                        </button>
                    @endif

                    @empty($commandBar)
                        @if(!$withoutApplyButton)
                            <button type="submit"
                                    id="submit-modal-{{$key}}"
                                    data-turbo="{{ var_export($turbo) }}"
                                    class="btn btn-default">
                                {{ $apply }}
                            </button>
                        @endif
                    @else
                        {!! $commandBar !!}
                    @endempty
                </div>
            </form>
        </div>
    </div>
@endpush
