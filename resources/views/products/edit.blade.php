@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container">
    <h5>Edit Product</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-8 g-0">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
        
                <div class="mb-3">
                    <label for="description" class="form-label">Product Description</label>
                    <textarea name="description" class="form-control" rows="5" required>{{ old('description', $product->description) }}</textarea>
                </div>
        
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>
        
                <div class="mb-3">
                    <label for="categories" class="form-label">Categories</label>
                    <select name="categories[]" id="categories" class="form-control" multiple="multiple" required>
                        @foreach ($allCategories as $category)
                            <option value="{{ $category->name }}" 
                                {{ in_array($category->name, old('categories', $product->categories->pluck('name')->toArray())) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage()">
                    @if ($product->image)
                        <img id="imagePreview" src="{{ Storage::url($product->image) }}" class="img-thumbnail mt-2" style="max-height: 200px" alt="{{ $product->name }}">
                    @endif
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-sm btn-primary">Update Product</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function previewImage() {
        const image = document.querySelector('input[name=image]');
        const imgPreview = document.querySelector('#imagePreview');

        if (image.files && image.files[0]) {
            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);

            oFReader.onload = function(oFREvent) {
                imgPreview.src = oFREvent.target.result;
            }
        }
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

