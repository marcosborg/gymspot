@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.contentPage.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.content-pages.update", [$contentPage->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="title">{{ trans('cruds.contentPage.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $contentPage->title) }}" required>
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.title_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="categories">{{ trans('cruds.contentPage.fields.category') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('categories') ? 'is-invalid' : '' }}" name="categories[]" id="categories" multiple>
                    @foreach($categories as $id => $category)
                        <option value="{{ $id }}" {{ (in_array($id, old('categories', [])) || $contentPage->categories->contains($id)) ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
                @if($errors->has('categories'))
                    <div class="invalid-feedback">
                        {{ $errors->first('categories') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.category_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="tags">{{ trans('cruds.contentPage.fields.tag') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('tags') ? 'is-invalid' : '' }}" name="tags[]" id="tags" multiple>
                    @foreach($tags as $id => $tag)
                        <option value="{{ $id }}" {{ (in_array($id, old('tags', [])) || $contentPage->tags->contains($id)) ? 'selected' : '' }}>{{ $tag }}</option>
                    @endforeach
                </select>
                @if($errors->has('tags'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tags') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.tag_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="page_text">{{ trans('cruds.contentPage.fields.page_text') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('page_text') ? 'is-invalid' : '' }}" name="page_text" id="page_text">{!! old('page_text', $contentPage->page_text) !!}</textarea>
                @if($errors->has('page_text'))
                    <div class="invalid-feedback">
                        {{ $errors->first('page_text') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.page_text_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="excerpt">{{ trans('cruds.contentPage.fields.excerpt') }}</label>
                <textarea class="form-control {{ $errors->has('excerpt') ? 'is-invalid' : '' }}" name="excerpt" id="excerpt">{{ old('excerpt', $contentPage->excerpt) }}</textarea>
                @if($errors->has('excerpt'))
                    <div class="invalid-feedback">
                        {{ $errors->first('excerpt') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.excerpt_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="featured_image">{{ trans('cruds.contentPage.fields.featured_image') }}</label>
                <div class="needsclick dropzone {{ $errors->has('featured_image') ? 'is-invalid' : '' }}" id="featured_image-dropzone">
                </div>
                @if($errors->has('featured_image'))
                    <div class="invalid-feedback">
                        {{ $errors->first('featured_image') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.featured_image_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('slider') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="slider" value="0">
                    <input class="form-check-input" type="checkbox" name="slider" id="slider" value="1" {{ $contentPage->slider || old('slider', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="slider">{{ trans('cruds.contentPage.fields.slider') }}</label>
                </div>
                @if($errors->has('slider'))
                    <div class="invalid-feedback">
                        {{ $errors->first('slider') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.slider_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('steps') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="steps" value="0">
                    <input class="form-check-input" type="checkbox" name="steps" id="steps" value="1" {{ $contentPage->steps || old('steps', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="steps">{{ trans('cruds.contentPage.fields.steps') }}</label>
                </div>
                @if($errors->has('steps'))
                    <div class="invalid-feedback">
                        {{ $errors->first('steps') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.steps_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('about') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="about" value="0">
                    <input class="form-check-input" type="checkbox" name="about" id="about" value="1" {{ $contentPage->about || old('about', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="about">{{ trans('cruds.contentPage.fields.about') }}</label>
                </div>
                @if($errors->has('about'))
                    <div class="invalid-feedback">
                        {{ $errors->first('about') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.about_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('call') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="call" value="0">
                    <input class="form-check-input" type="checkbox" name="call" id="call" value="1" {{ $contentPage->call || old('call', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="call">{{ trans('cruds.contentPage.fields.call') }}</label>
                </div>
                @if($errors->has('call'))
                    <div class="invalid-feedback">
                        {{ $errors->first('call') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.call_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('services') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="services" value="0">
                    <input class="form-check-input" type="checkbox" name="services" id="services" value="1" {{ $contentPage->services || old('services', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="services">{{ trans('cruds.contentPage.fields.services') }}</label>
                </div>
                @if($errors->has('services'))
                    <div class="invalid-feedback">
                        {{ $errors->first('services') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.services_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('gallery') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="gallery" value="0">
                    <input class="form-check-input" type="checkbox" name="gallery" id="gallery" value="1" {{ $contentPage->gallery || old('gallery', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="gallery">{{ trans('cruds.contentPage.fields.gallery') }}</label>
                </div>
                @if($errors->has('gallery'))
                    <div class="invalid-feedback">
                        {{ $errors->first('gallery') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.gallery_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('testimonial') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="testimonial" value="0">
                    <input class="form-check-input" type="checkbox" name="testimonial" id="testimonial" value="1" {{ $contentPage->testimonial || old('testimonial', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="testimonial">{{ trans('cruds.contentPage.fields.testimonial') }}</label>
                </div>
                @if($errors->has('testimonial'))
                    <div class="invalid-feedback">
                        {{ $errors->first('testimonial') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.testimonial_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('location') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="location" value="0">
                    <input class="form-check-input" type="checkbox" name="location" id="location" value="1" {{ $contentPage->location || old('location', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="location">{{ trans('cruds.contentPage.fields.location') }}</label>
                </div>
                @if($errors->has('location'))
                    <div class="invalid-feedback">
                        {{ $errors->first('location') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.location_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('faqs') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="faqs" value="0">
                    <input class="form-check-input" type="checkbox" name="faqs" id="faqs" value="1" {{ $contentPage->faqs || old('faqs', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="faqs">{{ trans('cruds.contentPage.fields.faqs') }}</label>
                </div>
                @if($errors->has('faqs'))
                    <div class="invalid-feedback">
                        {{ $errors->first('faqs') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contentPage.fields.faqs_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.content-pages.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $contentPage->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 0; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

<script>
    Dropzone.options.featuredImageDropzone = {
    url: '{{ route('admin.content-pages.storeMedia') }}',
    maxFilesize: 2, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    maxFiles: 1,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 2,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').find('input[name="featured_image"]').remove()
      $('form').append('<input type="hidden" name="featured_image" value="' + response.name + '">')
    },
    removedfile: function (file) {
      file.previewElement.remove()
      if (file.status !== 'error') {
        $('form').find('input[name="featured_image"]').remove()
        this.options.maxFiles = this.options.maxFiles + 1
      }
    },
    init: function () {
@if(isset($contentPage) && $contentPage->featured_image)
      var file = {!! json_encode($contentPage->featured_image) !!}
          this.options.addedfile.call(this, file)
      this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
      file.previewElement.classList.add('dz-complete')
      $('form').append('<input type="hidden" name="featured_image" value="' + file.file_name + '">')
      this.options.maxFiles = this.options.maxFiles - 1
@endif
    },
    error: function (file, response) {
        if ($.type(response) === 'string') {
            var message = response //dropzone sends it's own error messages in string
        } else {
            var message = response.errors.file
        }
        file.previewElement.classList.add('dz-error')
        _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
        _results = []
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i]
            _results.push(node.textContent = message)
        }

        return _results
    }
}

</script>
@endsection