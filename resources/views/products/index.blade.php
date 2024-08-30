@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h5>Products</h5>
        @can('manage-products')
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
        @endcan
    </div>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('products.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4 g-1">
                <input type="text" name="search" class="form-control" placeholder="Search data"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4 g-1">
                <select name="categories[]" id="categories" class="form-control" multiple="multiple">
                    @foreach ($allCategories as $category)
                        <option value="{{ $category->name }}" 
                            {{ in_array($category->name, request('categories', [])) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 g-1">
                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
            </div>
        </div>
    </form>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($products->count() == 0)
        <p>No products available</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Categories</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    @can('manage-products')
                    <th>Actions</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>IDR{{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>
                            @foreach($product->categories as $category)
                                <span class="badge bg-secondary">{{ $category->name }}</span>
                            @endforeach
                        </td>
                        <td>{{ $product->created_at->format('d-m-Y H:i:s') }}</td>
                        <td>{{ $product->updated_at->format('d-m-Y H:i:s') }}</td>
                        @can('manage-products')
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline-block"
                                onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>   
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $products->links() }}
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#categories').select2({
            placeholder: "Filter by categories",
            allowClear: true
        });
    });
</script>
@endsection

