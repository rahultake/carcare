<?php $__env->startSection('title', $product->name); ?>
<?php $__env->startSection('page-title', 'Product Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo e($product->name); ?></h1>
    <div class="btn-group">
        <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit Product
        </a>
        <button type="button" class="btn btn-outline-danger" onclick="deleteProduct()">
            <i class="fas fa-trash me-2"></i>Delete
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Product Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Name:</td>
                                <td><?php echo e($product->name); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">SKU:</td>
                                <td><code><?php echo e($product->sku); ?></code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Brand:</td>
                                <td><?php echo e($product->brand ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    <span class="badge bg-<?php echo e($product->status === 'active' ? 'success' : ($product->status === 'inactive' ? 'danger' : 'secondary')); ?>">
                                        <?php echo e(ucfirst($product->status)); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Featured:</td>
                                <td>
                                    <?php if($product->is_featured): ?>
                                        <span class="badge bg-warning"><i class="fas fa-star me-1"></i>Featured</span>
                                    <?php else: ?>
                                        <span class="text-muted">No</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Price:</td>
                                <td>
                                    <strong>$<?php echo e(number_format($product->price, 2)); ?></strong>
                                    <?php if($product->compare_price): ?>
                                        <br><small class="text-muted">
                                            Compare: <s>$<?php echo e(number_format($product->compare_price, 2)); ?></s>
                                        </small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Cost Price:</td>
                                <td><?php echo e($product->cost_price ? '$' . number_format($product->cost_price, 2) : '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Discount:</td>
                                <td><?php echo e($product->discount_percentage > 0 ? $product->discount_percentage . '%' : '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Weight:</td>
                                <td><?php echo e($product->weight ? $product->weight . ' kg' : '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Dimensions:</td>
                                <td>
                                    <?php if($product->length || $product->width || $product->height): ?>
                                        <?php echo e($product->length ?? '0'); ?> × <?php echo e($product->width ?? '0'); ?> × <?php echo e($product->height ?? '0'); ?> cm
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descriptions -->
        <?php if($product->short_description || $product->description): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Descriptions</h5>
                </div>
                <div class="card-body">
                    <?php if($product->short_description): ?>
                        <div class="mb-3">
                            <h6>Short Description</h6>
                            <p class="text-muted"><?php echo e($product->short_description); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($product->description): ?>
                        <div>
                            <h6>Full Description</h6>
                            <div><?php echo nl2br(e($product->description)); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- SEO Information -->
        <?php if($product->meta_title || $product->meta_description): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">SEO Information</h5>
                </div>
                <div class="card-body">
                    <?php if($product->meta_title): ?>
                        <div class="mb-3">
                            <h6>Meta Title</h6>
                            <p class="text-muted"><?php echo e($product->meta_title); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($product->meta_description): ?>
                        <div class="mb-3">
                            <h6>Meta Description</h6>
                            <p class="text-muted"><?php echo e($product->meta_description); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <h6>Slug</h6>
                        <code><?php echo e($product->slug); ?></code>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Inventory Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Inventory Status</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="display-4 fw-bold"><?php echo e($product->quantity); ?></div>
                    <div class="text-muted">Units in Stock</div>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Stock Status:</span>
                    <span class="badge bg-<?php echo e($product->stock_status === 'in_stock' ? 'success' : ($product->stock_status === 'low_stock' ? 'warning' : 'danger')); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $product->stock_status))); ?>

                    </span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Min. Quantity:</span>
                    <span><?php echo e($product->min_quantity); ?></span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Track Inventory:</span>
                    <span><?php echo e($product->track_inventory ? 'Yes' : 'No'); ?></span>
                </div>
                
                <?php if($product->isLowStock()): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Low Stock Alert!</strong> This product is running low.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Categories -->
        <?php if($product->categories->count() > 0): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $product->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-primary me-1 mb-1"><?php echo e($category->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tags -->
        <?php if($product->tags && count($product->tags) > 0): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $product->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-secondary me-1 mb-1"><?php echo e($tag); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Product Images -->
        <?php if($product->images->count() > 0): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Product Images</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6">
                                <div class="card position-relative">
                                    <img src="<?php echo e($image->image_url); ?>" class="card-img-top" 
                                         style="height: 120px; object-fit: cover; cursor: pointer;" 
                                         onclick="showImageModal('<?php echo e($image->image_url); ?>', '<?php echo e($image->alt_text); ?>')">
                                    <?php if($image->is_primary): ?>
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star"></i> Primary
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Product
                    </a>
                    
                    <?php if($product->status === 'active'): ?>
                        <form action="<?php echo e(route('admin.products.bulk-action')); ?>" method="POST" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="deactivate">
                            <input type="hidden" name="selected_ids[]" value="<?php echo e($product->id); ?>">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-eye-slash me-2"></i>Deactivate
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="<?php echo e(route('admin.products.bulk-action')); ?>" method="POST" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="activate">
                            <input type="hidden" name="selected_ids[]" value="<?php echo e($product->id); ?>">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-eye me-2"></i>Activate
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
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
                Are you sure you want to delete "<?php echo e($product->name); ?>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="<?php echo e(route('admin.products.destroy', $product)); ?>" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function showImageModal(imageUrl, altText) {
    document.getElementById('modalImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function deleteProduct() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u391893744/domains/gitcsdemoserver.online/public_html/carcare/resources/views/admin/products/show.blade.php ENDPATH**/ ?>