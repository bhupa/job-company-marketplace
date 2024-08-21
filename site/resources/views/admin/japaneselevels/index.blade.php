@extends('admin::index')

@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Japanese Levels</h3>
            <div class="box-tools">
                <div class="btn-group pull-right">
                    <a href="{{ route('admin.japanese-levels.create') }}" class="btn btn-sm btn-success">Add New</a>
                </div>
            </div>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($japaneseLevels as $level)
                        <tr>
                            <td>{{ $level->id }}</td>
                            <td>{{ $level->name }}</td>
                            <td>
                                <a href="{{ route('admin.japanese-levels.edit', $level->id) }}" class="btn btn-xs btn-primary">Edit</a>
                                <form action="{{ route('admin.japanese-levels.destroy', $level->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection