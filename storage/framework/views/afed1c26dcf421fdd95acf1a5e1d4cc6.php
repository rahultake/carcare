<?php $__env->startSection('title', 'Products'); ?>
<?php $__env->startSection('page-title', 'Product Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Products</h1>
    <div class="btn-group">
        <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Product
        </a>
        <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" 
                data-bs-toggle="dropdown">
            <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo e(route('admin.products.import')); ?>">
                <i class="fas fa-upload me-2"></i>Import Products</a></li>
            <li><a class="dropdown-item" href="<?php echo e(route('admin.products.export')); ?>">
                <i class="fas fa-download me-2"></i>Export Products</a></li>
            <li><a class="dropdown-item" href="<?php echo e(route('admin.products.low-stock')); ?>">
                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert</a></li>
        </ul>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.products.index')); ?>">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="category_id">
                        <option value="">All Categories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" 
                                    <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->full_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="brand">
                        <option value="">All Brands</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand); ?>" <?php echo e(request('brand') == $brand ? 'selected' : ''); ?>>
                                <?php echo e($brand); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="stock_status">
                        <option value="">All Stock</option>
                        <option value="in_stock" <?php echo e(request('stock_status') == 'in_stock' ? 'selected' : ''); ?>>In Stock</option>
                        <option value="low_stock" <?php echo e(request('stock_status') == 'low_stock' ? 'selected' : ''); ?>>Low Stock</option>
                        <option value="out_of_stock" <?php echo e(request('stock_status') == 'out_of_stock' ? 'selected' : ''); ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <form id="bulkActionForm" method="POST" action="<?php echo e(route('admin.products.bulk-action')); ?>">
            <?php echo csrf_field(); ?>
            <div class="row align-items-center">
                <div class="col-auto">
                    <span id="selectedCount">0</span> products selected
                </div>
                <div class="col-auto">
                    <select class="form-select" name="action" required>
                        <option value="">Choose Action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="draft">Move to Draft</option>
                        <option value="delete">Delete</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <button type="button" class="btn btn-secondary" onclick="clearSelection()">Clear</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        <?php if($products->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox" 
                                           value="<?php echo e($product->id); ?>">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($product->primary_image_url): ?>
                                            <img src="<?php echo e($product->primary_image_url); ?>" 
                                                 class="rounded me-3" width="50" height="50" 
                                                 style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo e($product->name); ?></strong>
                                            <?php if($product->is_featured): ?>
                                                <span class="badge bg-warning ms-1">Featured</span>
                                            <?php endif; ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo e($product->categories->pluck('name')->implode(', ')); ?>

                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code><?php echo e($product->sku); ?></code>
                                </td>
                                <td><?php echo e($product->brand ?: '-'); ?></td>
                                <td>
                                    <div>
                                        <?php echo e(number_format($product->price, 2)); ?>

                                        <?php if($product->has_discount): ?>
                                            <br><small class="text-muted">
                                                <s><?php echo e(number_format($product->compare_price, 2)); ?></s>
                                                <span class="text-danger">-<?php echo e($product->discount_percentage); ?>%</span>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo e($product->quantity); ?>

                                        <br>
                                        <span class="badge bg-<?php echo e($product->stock_status === 'in_stock' ? 'success' : ($product->stock_status === 'low_stock' ? 'warning' : 'danger')); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $product->stock_status))); ?>

                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e($product->status === 'active' ? 'success' : ($product->status === 'inactive' ? 'danger' : 'secondary')); ?>">
                                        <?php echo e(ucfirst($product->status)); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('admin.products.show', $product)); ?>" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.products.edit', $product)); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteProduct(<?php echo e($product->id); ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5>No products found</h5>
                <p class="text-muted">Start by adding your first product</p>
                <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Product
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

// Individual checkbox handling
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    const bulkActionsCard = document.getElementById('bulkActionsCard');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedCheckboxes.length > 0) {
        bulkActionsCard.style.display = 'block';
        selectedCount.textContent = selectedCheckboxes.length;
        
        // Update hidden inputs for bulk action
        const bulkForm = document.getElementById('bulkActionForm');
        const existingInputs = bulkForm.querySelectorAll('input[name="selected_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        selectedCheckboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = checkbox.value;
            bulkForm.appendChild(input);
        });
    } else {
        bulkActionsCard.style.display = 'none';
    }
}

function clearSelection() {
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

function deleteProduct(id) {
    document.getElementById('deleteForm').action = '/admin/products/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Bulk action form submission
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const action = this.querySelector('select[name="action"]').value;
    if (action === 'delete') {
        if (!confirm('Are you sure you want to delete the selected products? This action cannot be undone.')) {
            e.preventDefault();
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u391893744/domains/gitcsdemoserver.online/public_html/carcare/resources/views/admin/products/index.blade.php ENDPATH**/ ?>