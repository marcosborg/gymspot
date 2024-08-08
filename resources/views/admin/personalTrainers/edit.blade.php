@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.personalTrainer.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.personal-trainers.update", [$personalTrainer->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.personalTrainer.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $personalTrainer->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="email">{{ trans('cruds.personalTrainer.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', $personalTrainer->email) }}">
                @if($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="phone">{{ trans('cruds.personalTrainer.fields.phone') }}</label>
                <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text" name="phone" id="phone" value="{{ old('phone', $personalTrainer->phone) }}">
                @if($errors->has('phone'))
                    <div class="invalid-feedback">
                        {{ $errors->first('phone') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.phone_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="facebook">{{ trans('cruds.personalTrainer.fields.facebook') }}</label>
                <input class="form-control {{ $errors->has('facebook') ? 'is-invalid' : '' }}" type="text" name="facebook" id="facebook" value="{{ old('facebook', $personalTrainer->facebook) }}">
                @if($errors->has('facebook'))
                    <div class="invalid-feedback">
                        {{ $errors->first('facebook') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.facebook_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="instagram">{{ trans('cruds.personalTrainer.fields.instagram') }}</label>
                <input class="form-control {{ $errors->has('instagram') ? 'is-invalid' : '' }}" type="text" name="instagram" id="instagram" value="{{ old('instagram', $personalTrainer->instagram) }}">
                @if($errors->has('instagram'))
                    <div class="invalid-feedback">
                        {{ $errors->first('instagram') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.instagram_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="linkedin">{{ trans('cruds.personalTrainer.fields.linkedin') }}</label>
                <input class="form-control {{ $errors->has('linkedin') ? 'is-invalid' : '' }}" type="text" name="linkedin" id="linkedin" value="{{ old('linkedin', $personalTrainer->linkedin) }}">
                @if($errors->has('linkedin'))
                    <div class="invalid-feedback">
                        {{ $errors->first('linkedin') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.linkedin_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="tiktok">{{ trans('cruds.personalTrainer.fields.tiktok') }}</label>
                <input class="form-control {{ $errors->has('tiktok') ? 'is-invalid' : '' }}" type="text" name="tiktok" id="tiktok" value="{{ old('tiktok', $personalTrainer->tiktok) }}">
                @if($errors->has('tiktok'))
                    <div class="invalid-feedback">
                        {{ $errors->first('tiktok') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.tiktok_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.personalTrainer.fields.description') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{!! old('description', $personalTrainer->description) !!}</textarea>
                @if($errors->has('description'))
                    <div class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="photos">{{ trans('cruds.personalTrainer.fields.photos') }}</label>
                <div class="needsclick dropzone {{ $errors->has('photos') ? 'is-invalid' : '' }}" id="photos-dropzone">
                </div>
                @if($errors->has('photos'))
                    <div class="invalid-feedback">
                        {{ $errors->first('photos') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.photos_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="spots">{{ trans('cruds.personalTrainer.fields.spots') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('spots') ? 'is-invalid' : '' }}" name="spots[]" id="spots" multiple>
                    @foreach($spots as $id => $spot)
                        <option value="{{ $id }}" {{ (in_array($id, old('spots', [])) || $personalTrainer->spots->contains($id)) ? 'selected' : '' }}>{{ $spot }}</option>
                    @endforeach
                </select>
                @if($errors->has('spots'))
                    <div class="invalid-feedback">
                        {{ $errors->first('spots') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.spots_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="price">{{ trans('cruds.personalTrainer.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="number" name="price" id="price" value="{{ old('price', $personalTrainer->price) }}" step="0.01" required>
                @if($errors->has('price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.price_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.personalTrainer.fields.certificate_type') }}</label>
                @foreach(App\Models\PersonalTrainer::CERTIFICATE_TYPE_RADIO as $key => $label)
                    <div class="form-check {{ $errors->has('certificate_type') ? 'is-invalid' : '' }}">
                        <input class="form-check-input" type="radio" id="certificate_type_{{ $key }}" name="certificate_type" value="{{ $key }}" {{ old('certificate_type', $personalTrainer->certificate_type) === (string) $key ? 'checked' : '' }} required>
                        <label class="form-check-label" for="certificate_type_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
                @if($errors->has('certificate_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('certificate_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.certificate_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="professional_certificate">{{ trans('cruds.personalTrainer.fields.professional_certificate') }}</label>
                <input class="form-control {{ $errors->has('professional_certificate') ? 'is-invalid' : '' }}" type="text" name="professional_certificate" id="professional_certificate" value="{{ old('professional_certificate', $personalTrainer->professional_certificate) }}" required>
                @if($errors->has('professional_certificate'))
                    <div class="invalid-feedback">
                        {{ $errors->first('professional_certificate') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.professional_certificate_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="expiration">{{ trans('cruds.personalTrainer.fields.expiration') }}</label>
                <input class="form-control date {{ $errors->has('expiration') ? 'is-invalid' : '' }}" type="text" name="expiration" id="expiration" value="{{ old('expiration', $personalTrainer->expiration) }}" required>
                @if($errors->has('expiration'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expiration') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.expiration_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="user_id">{{ trans('cruds.personalTrainer.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $personalTrainer->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.personalTrainer.fields.user_helper') }}</span>
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
                xhr.open('POST', '{{ route('admin.personal-trainers.storeCKEditorImages') }}', true);
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
                data.append('crud_id', '{{ $personalTrainer->id ?? 0 }}');
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
    var uploadedPhotosMap = {}
Dropzone.options.photosDropzone = {
    url: '{{ route('admin.personal-trainers.storeMedia') }}',
    maxFilesize: 5, // MB
    acceptedFiles: '.jpeg,.jpg,.png,.gif',
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5,
      width: 4096,
      height: 4096
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="photos[]" value="' + response.name + '">')
      uploadedPhotosMap[file.name] = response.name
    },
    removedfile: function (file) {
      console.log(file)
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedPhotosMap[file.name]
      }
      $('form').find('input[name="photos[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($personalTrainer) && $personalTrainer->photos)
      var files = {!! json_encode($personalTrainer->photos) !!}
          for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          this.options.thumbnail.call(this, file, file.preview ?? file.preview_url)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="photos[]" value="' + file.file_name + '">')
        }
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