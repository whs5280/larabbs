@if(count($errors) > 0)
    <div class="alert alert-danger">
        <ul class="mt-2 mb-2">
            @foreach($errors->all() as $error)
                <li style="list-style: none;"><i class="glyphicon glyphicon-remove"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif()