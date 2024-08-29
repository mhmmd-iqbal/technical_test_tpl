@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="container">
    <h5>Create Product</h5>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-8 g-0">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Product Description</label>
                    <textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                </div>

                <div class="mb-3">
                    <label for="categories" class="form-label">Categories</label>
                    <select name="categories[]" id="categories" class="form-control" multiple="multiple" required>
                        @foreach ($allCategories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(event)" accept="image/*">
                    <img id="preview" src="" style="max-height: 200px; margin-top: 10px;" />
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-sm btn-primary">Create Product</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(event) {
        var image = document.getElementById('preview');
        image.src = URL.createObjectURL(event.target.files[0]);
    }

    $(document).ready(function() {
        $('#categories').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: "Select or add categories"
        });
    });
</script>
@endsection
