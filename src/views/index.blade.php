@extends($layout)
@section('content')
    <h1>Import CSV Content</h1>
    <div class="imports-index">
        <form action="" method="POST" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
            <input type="hidden" name="_token" value={{csrf_token()}}>
            @foreach($fields as $field)
                <fieldset class="form-group">
                    <label for="{{$field}}" class="control-label col-sm-2">
                        {{ucwords(str_replace('_',' ',$field)).' CSV:'}}
                    </label>
                    <div class="col-sm-10">
                        <div class="form-control-static">
                            <input type="file" name="{{$field}}"/>
                        </div>
                    </div>
                </fieldset>
            @endforeach
        <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
@stop