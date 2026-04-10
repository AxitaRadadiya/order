<a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
<a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" title="Delete">
  <i class="fas fa-trash"></i>
</button>
