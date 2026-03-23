{{--
  The edit view reuses the same form template as create.
  Both share the same Blade file (create.blade.php) which
  checks for $product via isset($product).
  This file simply loads that shared template.
--}}
@include('admin.products.create')
