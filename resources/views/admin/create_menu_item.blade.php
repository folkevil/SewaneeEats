@extends('admin.layout')

@section('head')
    Menu
@stop

@section('body')

    <h1>Create Menu Item</h1>

    <form action="{{ route('createMenuItem') }}" method="post">
        {{ csrf_field() }}
        <input name="restaurant_id" type="hidden" value="{{ $restaurant->id }}">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" maxlength="75" required>

            <label for="price">Price</label>
            <input type="number" step=".01" min="0" id="price" class="form-control" name="price" maxlength="6" required>

            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" maxlength="200" required>

            <label for="hours-table">Specify the hours during which this menu item is sold by the restaurant. If a menu
                item is available during
                disjoint times use the extra rows for that day to fill that in. Fill each cell in in this form:
                "hh:mm-hh:mm" or put "closed" if the menu item is not available on that day</label>
            @include('partials.create_available_times')
            <div class="category-group">
                <div id="select-category-parent">
                    <label for="select-category">Which category of food does it belong to?</label>
                    <select name="category_id" id="select-category" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="create-category-parent">
                    <label for="create-category">Create a new category</label>
                    <input type="text" class="form-control" id="create-category" name="create_category">
                </div>
            </div>
            <button class="btn btn-primary" type="button" onclick="handleCategory()" id="toggle-category">
                Or create a new category
            </button>
            <button class="btn btn-primary" id="create-item">
                Create Menu Item
            </button>
        </div>
    </form>

    <style>
        button {
            margin-top: 10px;
        }
    </style>

    <script src="{{ asset('js/admin/create_update_menu_item.js') }}"></script>
@stop