<?php $__env->startSection('title', 'Blogs'); ?>
<?php $__env->startSection('page-title', 'Blogs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Edit Blog</h2>

    <form action="<?php echo e(route('admin.blogs.update', $blog)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo e($blog->title); ?>" required>
        </div>

        <div class="form-group">
            <label>Category *</label>
            <select name="category_id" class="form-control" required>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e($cat->id == $blog->category_id ? 'selected' : ''); ?>>
                        <?php echo e($cat->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group">
            <label>Short Description</label>
            <textarea name="short_description" class="form-control"><?php echo e($blog->short_description); ?></textarea>
        </div>

        <div class="form-group">
            <label>Long Description</label>
            <textarea name="long_description" id="description" class="form-control" rows="6"><?php echo e($blog->long_description); ?></textarea>
        </div>

        <div class="form-group">
            <label>Image</label><br>
            <?php if($blog->image): ?>
                <img src="<?php echo e(asset($blog->image)); ?>" width="100" class="mb-2">
            <?php endif; ?>
            <input type="file" name="image" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="<?php echo e(route('admin.blogs.index')); ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u391893744/domains/gitcsdemoserver.online/public_html/carcare/resources/views/admin/blogs/edit.blade.php ENDPATH**/ ?>