@extends('layouts.admin')
@section('content')
@can('content_page_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.content-pages.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.contentPage.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.contentPage.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ContentPage">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.title') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.featured_image') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.slider') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.steps') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.about') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.call') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.services') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.gallery') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.testimonial') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.location') }}
                    </th>
                    <th>
                        {{ trans('cruds.contentPage.fields.faqs') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
            </thead>
        </table>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('content_page_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.content-pages.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.content-pages.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'title', name: 'title' },
{ data: 'featured_image', name: 'featured_image', sortable: false, searchable: false },
{ data: 'slider', name: 'slider' },
{ data: 'steps', name: 'steps' },
{ data: 'about', name: 'about' },
{ data: 'call', name: 'call' },
{ data: 'services', name: 'services' },
{ data: 'gallery', name: 'gallery' },
{ data: 'testimonial', name: 'testimonial' },
{ data: 'location', name: 'location' },
{ data: 'faqs', name: 'faqs' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-ContentPage').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
let visibleColumnsIndexes = null;
$('.datatable thead').on('input', '.search', function () {
      let strict = $(this).attr('strict') || false
      let value = strict && this.value ? "^" + this.value + "$" : this.value

      let index = $(this).parent().index()
      if (visibleColumnsIndexes !== null) {
        index = visibleColumnsIndexes[index]
      }

      table
        .column(index)
        .search(value, strict)
        .draw()
  });
table.on('column-visibility.dt', function(e, settings, column, state) {
      visibleColumnsIndexes = []
      table.columns(":visible").every(function(colIdx) {
          visibleColumnsIndexes.push(colIdx);
      });
  })
});

</script>
@endsection