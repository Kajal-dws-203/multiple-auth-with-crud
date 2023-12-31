@extends('admin.layout.app')

@include('admin.layout.header')
@section('content')
<div class="container pt-5">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="pt-3">
        <form action="{{ route('user.store')}}" method="post" id="userForm" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="mb-3 col-md-6 form-group">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 col-md-6 form-group">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="email"
                        value="{{old('email')}}" multiple>
                    @error('email')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 form-group">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    @error('password')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 col-md-6 form-group">
                    <label for="exampleInputPassword1" class="form-label">Image</label>
                    <input type="file" class="form-control" id="photo" name="photo[]" multiple>
                    <span class="images-preview-div"> </span>
                    {{-- <img src="#" id="preview_img" width="200px" style="display:none;" /> --}}
                    @error('photo')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div id="add_more_feild">
                @forelse (old('multiple_addresses', []) as $key => $input)
                @include('admin.user.clone-column', ['rowIndex' => $key++, 'oldAddress' => $input['address'],
                'totalItem' => count(old('multiple_addresses', []))])
                @empty
                @include('admin.user.clone-column', ['rowIndex' => '1'])
                @endforelse
            </div>
            <div>
                <button type="submit" class="btn btn-success">Submit</button>
                <a type="buttton" class="btn btn-danger" href="{{ route('admin.dashboard') }}">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function (){

            // for validation
            $('#userForm').validate({
                rules: {
                    name : {
                        required : true,
                        maxlength : 255,
                    },
                    email : {
                        required : true,
                        email : true
                    },
                    password : {
                        required : true,
                        minlength : 6
                    },
                    "photo[]": {
                        accept: "image/jpg,image/jpeg,image/png",
                    }
                },
                messages : {
                    name : {
                        required : "Name is Required",
                        maxlength : "Name cannot be more than 255 characters"
                    },
                    email : {
                        required : "Email is required",
                        email : "Email must be a valid email address"
                    },
                    password : {
                        required : "Password is required",
                        minlength : "Password must be at least 6 characters"
                    },
                    photo : {
                        accept: "Please upload file in these format only (jpg, jpeg, png).", 
                        max: "Image size cannot be more than 2048 bytes"
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            //for add new row dynamically
            $(document).on('click','.add_field_button',function (){
                let id = $(this).attr('id');
                let rowIndexValue = id.split('_').pop();
                let rowIndex = parseInt(rowIndexValue) + 1;

                let url = "{{ route('user.clone.column') }}";
                $.ajax({
                    url : url,
                    type : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data : {
                        rowIndex : rowIndex,
                    },
                    success : function(res) {
                        if (res && res.success) {
                            $('#add_more_feild').append(res.html);
                            var numItems = $('.item').length;
                            if (numItems > 0) {
                                console.log("rowIndexValue",rowIndexValue);
                                console.log("rowIndex",rowIndex);
                                $('#add_more_' + rowIndexValue).css("display", "none");
                                $('#remove_' + rowIndexValue).css("display", "block");
                                $('#remove_' + rowIndex).css("display", "block");
                            }
                            else{
                                console.log("in else part");
                            }
                        } else {
                            console.log('error', res.message);
                        }
                    },
                    error: function (res) {
                        console.log('error', res.message);
                    },
                })
            });

            //for remove row
            $(document).on('click','.remove_field_button', function (){
                let itemLength = $('.item').length;               
                let rowId = $(this).attr('id');
                let rowIndexId = rowId.split('_');
                if (rowIndexId.length > 0) {
                    $('#item_' + rowIndexId[1]).remove();
                }

                $('.item').each(function (index) {
                    var cloneIndex = index + 1;
                    $(this).attr('id', 'item_' + cloneIndex);
                    $(this).find(':input,textarea').each(function (i) {
                        let inputId = $(this).attr('id').slice(0, -1);
                        // console.log("Input id is",inputId);
                        $(this).attr('id', inputId + cloneIndex);
                        $(this).attr('name', 'multiple_addresses[' + cloneIndex + '][' + inputId + ']');
                    });
                });
                var rowIndexNum = rowIndexId[rowIndexId.length-1];
                // $('div.add_more_btn').attr('id','add_more_btn_' + rowIndexNum);

                if(itemLength == rowIndexNum){
                    $('#add_more_' + (rowIndexNum-1)).show();
                    $('#remove_' + (rowIndexNum-1)).show();    
                }
                if(itemLength == 2){
                    $('#remove_' + (itemLength - 1)).hide();
                }
            });

            //for preview image
            // photo.onchange = evt => {
            //     preview = document.getElementById('preview_img');
            //     preview.style.display = 'block';
            //     const [file] = photo.files
            //     if (file) {
            //         preview.src = URL.createObjectURL(file)
            //     }
            // }
            
            // for preview mutiple image
            // var previewImages = function(input, imgPreviewPlaceholder) {

            //     if (input.files) {
            //         var filesAmount = input.files.length;

            //         for (i = 0; i < filesAmount; i++) {
            //             var reader = new FileReader();

            //             reader.onload = function(event) {
            //                 $($.parseHTML('<img>')).attr('src', event.target.result).css({'width': '100px', 'height': '100px', 'margin' : '2px', 'border' : '1px s'}).appendTo(imgPreviewPlaceholder);
            //             }

            //             reader.readAsDataURL(input.files[i]);
            //         }
            //     }
            // };

            // $('#photo').on('change', function() {
            //     previewImages(this, 'span.images-preview-div');
            // });
        });
</script>
@endsection