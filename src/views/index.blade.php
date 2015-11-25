@extends($layout)
@section('content')
    <div class="imports-index">
        {!!Form::open(['files'=>'true','class' => 'form-horizontal'])!!}
        @foreach($fields as $field)
            <fieldset class="form-group">
                {!! Form::label($field,ucwords(str_replace('_',' ',$field)).' CSV:',['class'=>'control-label col-sm-2']) !!}
                <div class="col-sm-10">
                    <div class="form-control-static">
                        {!! Form::file($field,null,['class'=>'form-control'])!!}
                    </div>
                </div>
            </fieldset>
        @endforeach
        <button type="submit" class="btn btn-success" onclick="confirm('Are You Sure?')">Submit</button>
        {!!Form::close()!!}
    </div>
@stop