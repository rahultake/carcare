<?php $__env->startSection('title', 'Categories'); ?>
<?php $__env->startSection('page-title', 'Category Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Categories</h1>
    <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Category
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if($categories->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover" id="categoriesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-id="<?php echo e($category->id); ?>">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($category->icon): ?>
                                            <i class="<?php echo e($category->icon); ?> me-2 text-primary"></i>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo e($category->name); ?></strong>
                                            <?php if($category->description): ?>
                                                <br><small class="text-muted"><?php echo e(Str::limit($category->description, 50)); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php echo e($category->parent ? $category->parent->name : '-'); ?>

                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($category->products->count()); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e($category->status === 'active' ? 'success' : 'secondary'); ?>">
                                        <?php echo e(ucfirst($category->status)); ?>

                                    </span>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm sort-order" 
                                           value="<?php echo e($category->sort_order); ?>" style="width: 80px;" data-id="<?php echo e($category->id); ?>">
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteCategory(<?php echo e($category->id); ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Child categories -->
                            <?php $__currentLoopData = $category->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-id="<?php echo e($child->id); ?>" class="table-light">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-3">└─</span>
                                            <?php if($child->icon): ?>
                                                <i class="<?php echo e($child->icon); ?> me-2 text-primary"></i>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo e($child->name); ?></strong>
                                                <?php if($child->description): ?>
                                                    <br><small class="text-muted"><?php echo e(Str::limit($child->description, 50)); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo e($child->parent->name); ?></td>
                                    <td><span class="badge bg-info"><?php echo e($child->products->count()); ?></span></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($child->status === 'active' ? 'success' : 'secondary'); ?>">
                                            <?php echo e(ucfirst($child->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm sort-order" 
                                               value="<?php echo e($child->sort_order); ?>" style="width: 80px;" data-id="<?php echo e($child->id); ?>">
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('admin.categories.edit', $child)); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteCategory(<?php echo e($child->id); ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5>No categories found</h5>
                <p class="text-muted">Start by creating your first category</p>
                <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Category
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
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category? This action cannot be undone.
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
function deleteCategory(id) {
    document.getElementById('deleteForm').action = '/admin/categories/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Auto-save sort order changes
document.querySelectorAll('.sort-order').forEach(function(input) {
    input.addEventListener('change', function() {
        const id = this.dataset.id;
        const sortOrder = this.value;
        
        fetch('/admin/categories/sort-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                orders: [{id: id, sort_order: sortOrder}]
            })
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u391893744/domains/gitcsdemoserver.online/public_html/carcare/resources/views/admin/categories/index.blade.php ENDPATH**/ ?>